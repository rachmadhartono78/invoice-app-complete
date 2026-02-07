<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappNotification;
use App\Jobs\SendWhatsappNotifQiscus;
use App\Mail\EmailVerification;
use App\Models\TokenVerification;
use App\Models\User;
use App\Models\UserIdentifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $status = 500;
        $result = false;
        $credentials = $request->only('email', 'password');

        $request->validate([
            'device_name' => 'required|string|max:255',
        ]);

        // First try normal authentication
        if (Auth::attempt($credentials)) {
            try {
                // Get authenticated user directly (faster than querying again)
                $data = auth()->user();
                
                // Check if email is verified
                if (!$data->email_verified_at) {
                    $status = 403;
                    $result = false;
                    $message = 'Email belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu.';
                    $data = null;
                } else {
                    $token = $data->createToken($request->device_name)->plainTextToken;
                    // Load authorities with optimized query (reduced nested relationships)
                    $this->getDataApplications($data);

                    $status = 200;
                    $result = true;
                    $message = 'Login sukses';
                    $data['token'] = $token;
                }
            } catch (\Throwable $th) {
                Log::error([
                    'error' => [
                        'file' => $th->getFile(),
                        'line' => $th->getLine(),
                        'code' => $th->getCode(),
                        'message' => $th->getMessage(),
                    ],
                ]);
                $message = $th->getMessage();
                $data = null;
            }
        } else {
            // Check if email exists in user_identifiers (must be verified)
            $identifier = \App\Models\UserIdentifier::where('type', 'email')
                ->where('value', $credentials['email'])
                ->whereNull('deleted_at')
                ->whereNotNull('verified_at')
                ->first();

            if ($identifier) {
                // Get user and verify password
                $user = User::find($identifier->user_id);

                if ($user && Hash::check($credentials['password'], $user->password)) {
                    try {
                        $token = $user->createToken($request->device_name)->plainTextToken;
                        $this->getDataApplications($user);

                        $status = 200;
                        $result = true;
                        $message = 'Login sukses';
                        $user['token'] = $token;
                        $data = $user;
                    } catch (\Throwable $th) {
                        Log::error([
                            'error' => [
                                'file' => $th->getFile(),
                                'line' => $th->getLine(),
                                'code' => $th->getCode(),
                                'message' => $th->getMessage(),
                            ],
                        ]);
                        $message = $th->getMessage();
                        $data = null;
                    }
                } else {
                    $status = 401;
                    $message = 'Email atau password salah!';
                    $data = null;
                }
            } else {
                $status = 401;
                $message = 'Email atau password salah!';
                $data = null;
            }
        }

        $response = [
            'result' => $result,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Check if user already exists
            $existingUser = User::where('email', $request->email)->first();

            if ($existingUser) {
                // If user exists and is already verified
                if ($existingUser->email_verified_at) {
                    return response()->json([
                        'result' => false,
                        'message' => 'Email sudah terdaftar dan terverifikasi. Silakan login.',
                    ], 422);
                }

                // If user exists but not verified, check rate limit
                $oneDayAgo = Carbon::now()->subDay();
                $attemptCount = TokenVerification::where('identifier', $request->email)
                    ->where('type', 'email')
                    ->where('created_at', '>=', $oneDayAgo)
                    ->count();

                if ($attemptCount >= 5) {
                    return response()->json([
                        'result' => false,
                        'message' => 'Anda telah mencapai batas maksimal pengiriman email verifikasi (5x dalam 24 jam). Silakan coba lagi besok.',
                    ], 429);
                }

                // Use existing user
                $user = $existingUser;
                $isNewUser = false;
            } else {
                DB::beginTransaction();
                try {
                    // Create new user
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                    ]);

                    $user->authorities()->attach([
                        'f18fa99e-8d3c-4b6b-91a3-9c0000000003' => ['id' => \Illuminate\Support\Str::uuid()]
                    ]); 
                    
                    $isNewUser = true;
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }

            // Generate OTP code
            $otpCode = rand(100000, 999999);

            // Store verification token
            TokenVerification::create([
                'type' => 'email',
                'identifier' => $request->email,
                'token' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(5),
                'used' => false,
            ]);

            // Create verification URL
            $verificationUrl = config('app.url').'/app/verify-email?token='.$otpCode.'&email='.urlencode($request->email);

            // Determine email recipient (redirect to test email in non-production)
            $emailRecipient = config('app.env') === 'production' ? $user->email : '211232638@uii.ac.id';

            // Log email redirection in non-production environments
            if (config('app.env') !== 'production') {
                Log::info("Email redirected from {$user->email} to {$emailRecipient} (non-production environment)");
            }

            // Send verification email
            // Mail::to($emailRecipient)->send(new EmailVerification($otpCode, $verificationUrl, $user->name));

            $message = $isNewUser
                ? 'Pendaftaran berhasil! Silakan cek email Anda untuk verifikasi.'
                : 'Email verifikasi telah dikirim ulang. Silakan cek email Anda.';

            // Show OTP in non-production environments
            if (config('app.env') !== 'production') {
                $message .= ' OTP: ' . $otpCode;
            }

            return response()->json([
                'result' => true,
                'message' => $message,
                'data' => [
                    'email' => $user->email,
                    'is_new_user' => $isNewUser,
                ],
            ], $isNewUser ? 201 : 200);
        } catch (\Throwable $th) {
            Log::error('Registration Error: '.$th->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $token = $user->currentAccessToken();

            if ($token) {
                $token->update(['expires_at' => now()]);
            }

            // if ($token && $user->tokens()->count() > 1) {
            //     $user->tokens()->whereNot('id', $token->id)->delete();
            //     $token->update(['expires_at' => now()]);
            // } else {
            //     $token->update(['expires_at' => now()]);
            // }

            // Tidak perlu Auth::guard('web')->logout() kalau pakai token API

            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout',
            ]);
        } catch (\Throwable $th) {
            Log::error('Logout Error: '.$th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to logout',
            ], 500);
        }
    }

    public function googleCallback(Request $request)
    {
        $status = 500;
        $user = null;
        $clientId = config('services.google.client_id');
        $credential = $request->credential;

        $request->validate([
            'device_name' => 'required|string|max:255',
        ]);

        try {
            $response = Http::asForm()->post('https://www.googleapis.com/oauth2/v3/tokeninfo', [
                'id_token' => $credential,
            ]);
            $googleUser = $response->json();
            if ($response->json('aud') === $clientId) {
                // Check primary email first
                $user = User::where('email', $googleUser['email'])->first();

                // If not found, check in user_identifiers (must be verified)
                if (! $user) {
                    $identifier = UserIdentifier::where('type', 'email')
                        ->where('value', $googleUser['email'])
                        ->whereNull('deleted_at')
                        ->whereNotNull('verified_at')
                        ->first();

                    if ($identifier) {
                        $user = User::find($identifier->user_id);
                    }
                }

                if ($user) {
                    $dataToUpdate = ['avatar' => $googleUser['picture'] ?? null];
                    if (! $user->google_id) {
                        $dataToUpdate['google_id'] = $googleUser['sub'];
                    }
                    $user->update($dataToUpdate);
                    // $user->load('roles');
                    // Auth::login($user);
                    $user['token'] = $user->createToken($request->device_name)->plainTextToken;
                    $this->getDataApplications($user);
                    $message = 'Berhasil masuk';
                    $status = 200;
                } else {
                    DB::beginTransaction();
                    $user = User::create([
                        'name' => $googleUser['name'],
                        'email' => $googleUser['email'],
                        'avatar' => $googleUser['picture'] ?? null,
                        'google_id' => $googleUser['sub'],
                        'password' => Hash::make($googleUser['email']),
                        'email_verified_at' => now(),
                    ]);
                     $user->authorities()->attach([
                        'f18fa99e-8d3c-4b6b-91a3-9c0000000003' => ['id' => \Illuminate\Support\Str::uuid()]
                    ]); 
                    $user['token'] = $user->createToken($request->device_name)->plainTextToken;
                    DB::commit();
                    $this->getDataApplications($user);
                    $message = 'Berhasil mendaftar';
                    $status = 201;
                }
            } else {
                $message = ($response->json('aud') == $clientId).' '.$clientId;
                $status = 401;
            }
        } catch (\Throwable $th) {
            $m = 'App name: '.(config('app.name') ?? null)."\nDate time : ".now()."\n\nFile : ".$th->getFile()."\nLine : ".$th->getLine()."\nCode : ".$th->getCode()."\nMessage : ".$th->getMessage();
            Log::info(['error' => $m]);

            $message = $th->getMessage();
        }
        $response = [
            'data' => $user,
            'message' => $message,
        ];
        Log::info('SUKSES');

        return response()->json($response, $status);
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        $phone = $request->phone_number;
        $countryCode = $request->country_code ?? '62';

        // Check if phone number exists in users table
        $user = User::where('phone', $phone)->first();

        // If not found, check in user_identifiers (must be verified)
        if (! $user) {
            $identifier = \App\Models\UserIdentifier::where('type', 'phone')
                ->where('value', $phone)
                ->whereNull('deleted_at')
                ->whereNotNull('verified_at')
                ->first();

            if ($identifier) {
                $user = User::find($identifier->user_id);
            }
        }

        if (! $user) {
            return response()->json([
                'message' => 'Akun tidak ditemukan',
            ], 404);
        }

        // Check OTP limit (3 times in 3 hours)
        $threeHoursAgo = Carbon::now()->subHours(3);
        $otpCount = TokenVerification::where('identifier', $phone)
            ->where('type', 'whatsapp')
            ->where('created_at', '>=', $threeHoursAgo)
            ->count();

        if ($otpCount >= 3) {
            return response()->json([
                'message' => 'Anda telah mencapai batas maksimal permintaan OTP (3x dalam 3 jam). Silakan coba lagi nanti.',
            ], 429);
        }

        $otp = rand(100000, 999999); // kode 6 digit

        // Simpan OTP di DB
        TokenVerification::updateOrCreate(
            ['identifier' => $phone, 'used' => false],
            [
                'type' => 'whatsapp',
                'token' => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
                'used' => false,
            ]
        );

        // Kirim OTP ke WA menggunakan layanan WA API (misal Twilio)
        $message = '[KOAS] '.$otp.'-OTP untuk verifikasi nomor WhatsApp Anda. Kode ini berlaku selama 5 menit.';
        // $message = '[KOAS] Kode OTP login Anda adalah: '.$otp.' (berlaku 5 menit). Mohon jangan bagikan kode ini kepada siapapun demi keamanan akun Anda.';

        // For local development, return OTP in response
        if (config('app.env') === 'local') {
            return response()->json([
                'message' => 'OTP '.$otp,
                'otp' => $otp,
            ]);
        }

        // try {
        //     SendWhatsappNotification::dispatch($phone, $message, $countryCode);
        //     return response()->json(['message' => 'OTP terkirim via WhatsApp']);
        // } catch (\Exception $e) {
        //     Log::error('WhatsApp API error: ' . $e->getMessage());
        //     return response()->json(['message' => 'Gagal mengirim OTP. Silakan coba lagi.'], 500);
        // }

        SendWhatsappNotifQiscus::dispatch(
            $phone,
            'otp',
            [
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $otp,
                        ],

                    ],
                ],
                [
                    'type' => 'button',
                    'sub_type' => 'url',
                    'index' => '0',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $otp,
                        ],

                    ],
                ],
            ]
        );
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp_code' => 'required|string',
        ]);

        $otpEntry = TokenVerification::where('identifier', $request->phone_number)
            ->where('token', $request->otp_code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $otpEntry) {
            return response()->json(['message' => 'OTP tidak valid atau sudah expired'], 422);
        }

        $otpEntry->used = true;
        $otpEntry->used_at = Carbon::now();
        $otpEntry->save();

        // Check if phone number exists in users table
        $data = User::where('phone', $request->phone_number)->first();

        // If not found, check in user_identifiers (must be verified)
        if (! $data) {
            $identifier = \App\Models\UserIdentifier::where('type', 'phone')
                ->where('value', $request->phone_number)
                ->whereNull('deleted_at')
                ->whereNotNull('verified_at')
                ->first();

            if ($identifier) {
                $data = User::find($identifier->user_id);
            }
        }

        // If still not found, create new user
        if (! $data) {
            $data = User::create([
                'name' => $request->phone_number,
                'email' => $request->phone_number.'@ecos.com',
                'phone' => $request->phone_number,
                'password' => Hash::make($request->phone_number),
            ]);
        }

        // Generate token Sanctum
        $token = $data->createToken('otp wa'.$request->device_name)->plainTextToken;
        $data['token'] = $token;
        $this->getDataApplications($data);

        return response()->json([
            'message' => 'Login berhasil',
            'data' => $data,
        ]);
    }

    public function updatePhoneNumber(Request $request)
    {
        $status = 500;
        $result = false;
        $data = null;
        $validatedData = $this->validate($request, [
            'phone_number' => 'required',
        ]);
        try {
            $validatedData['password'] = Hash::make($request->phone_number);
            $user = auth()->user()->update($validatedData);
            $data = auth()->user();
            $data->load('roles');
            $result = true;
            $status = 200;
            $message = 'success_update_record';
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        $response = [
            'result' => $result,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    public function sendEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Email tidak ditemukan',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email sudah terverifikasi',
            ], 400);
        }

        // Check rate limit (5 times per day)
        $oneDayAgo = Carbon::now()->subDay();
        $attemptCount = TokenVerification::where('identifier', $request->email)
            ->where('type', 'email')
            ->where('created_at', '>=', $oneDayAgo)
            ->count();

        if ($attemptCount >= 5) {
            return response()->json([
                'message' => 'Anda telah mencapai batas maksimal pengiriman email verifikasi (5x dalam 24 jam). Silakan coba lagi besok.',
            ], 429);
        }

        try {
            // Generate OTP code
            $otpCode = rand(100000, 999999);

            // Store verification token
            TokenVerification::create([
                'type' => 'email',
                'identifier' => $request->email,
                'token' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(5),
                'used' => false,
            ]);

            // Create verification URL
            $verificationUrl = config('app.url').'/app/verify-email?token='.$otpCode.'&email='.urlencode($request->email);

            // Determine email recipient (redirect to test email in non-production)
            $emailRecipient = config('app.env') === 'production' ? $user->email : '211232638@uii.ac.id';

            // Log email redirection in non-production environments
            if (config('app.env') !== 'production') {
                Log::info("Email redirected from {$user->email} to {$emailRecipient} (non-production environment)");
            }

            // Send verification email
            Mail::to($emailRecipient)->send(new EmailVerification($otpCode, $verificationUrl, $user->name));

            return response()->json([
                'message' => 'Kode verifikasi telah dikirim ke email Anda',
            ]);
        } catch (\Throwable $th) {
            Log::error('Send Email Verification Error: '.$th->getMessage());

            return response()->json([
                'message' => 'Gagal mengirim email verifikasi',
            ], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string',
        ]);

        $otpEntry = TokenVerification::where('identifier', $request->email)
            ->where('type', 'email')
            ->where('token', $request->otp_code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $otpEntry) {
            return response()->json([
                'message' => 'Kode OTP tidak valid atau sudah expired',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        // Mark OTP as used
        $otpEntry->used = true;
        $otpEntry->used_at = Carbon::now();
        $otpEntry->save();

        // Verify email
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json([
            'result' => true,
            'message' => 'Email berhasil diverifikasi! Silakan login.',
        ]);
    }

    public function getDataApplications($user)
    {
        return $user->load('organizations', 'authorities.application', 'authorities.menus.actions.action', 'authorities.organizations', 'authorities.menus.menuInduk', 'authorities.menus.application');
    }

    public function redirectToYahoo()
    {
        return Socialite::driver('yahoo')->stateless()->redirect();
    }

    public function handleYahooCallback()
    {
        $yahooUser = Socialite::driver('yahoo')->stateless()->user();

        // Check primary email first
        $user = User::where('email', $yahooUser->getEmail())->first();

        // If not found, check in user_identifiers (must be verified)
        if (! $user) {
            $identifier = \App\Models\UserIdentifier::where('type', 'email')
                ->where('value', $yahooUser->getEmail())
                ->whereNull('deleted_at')
                ->whereNotNull('verified_at')
                ->first();

            if ($identifier) {
                $user = User::find($identifier->user_id);
            }
        }

        if ($user) {
            $dataToUpdate = ['avatar' => $yahooUser->getAvatar() ?? null];
            if (! $user->yahoo_id) {
                $dataToUpdate['yahoo_id'] = $yahooUser->getId();
            }
            $user->update($dataToUpdate);
            $user['token'] = $user->createToken('yahoo')->plainTextToken;
            $this->getDataApplications($user);
            $message = 'Berhasil masuk';
            $status = 200;
        } else {
            DB::beginTransaction();
            $user = User::create([
                'name' => $yahooUser->getName() ?? $yahooUser->getNickname(),
                'email' => $yahooUser->getEmail(),
                'avatar' => $yahooUser->getAvatar() ?? null,
                'yahoo_id' => $yahooUser->getId(),
                'password' => Hash::make($yahooUser->getEmail()),
                'email_verified_at' => now(),
            ]);
            $user['token'] = $user->createToken('unknown')->plainTextToken;

            $user->authorities()->attach([
                        'f18fa99e-8d3c-4b6b-91a3-9c0000000003' => ['id' => \Illuminate\Support\Str::uuid()]
                    ]); 

            DB::commit();
            $this->getDataApplications($user);
            $message = 'Berhasil mendaftar';
            $status = 201;
        }

        Auth::login($user);

        return redirect(config('app.url').'/login-success?token='.$user->createToken('token')->plainTextToken);
    }

    public function checkToken(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Load authorities for compatibility (but use lazy loading in other places)
            $user->load('organizations', 'authorities.application', 'authorities.menus.actions.action', 'authorities.organizations', 'authorities.menus.menuInduk', 'authorities.menus.application');
            $user['token'] = $request->bearerToken();

            return response()->json([
                'valid' => true,
                'user' => $user,
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Token tidak valid atau sudah expired',
        ], 401);
    }

    // Load application data (permissions, menus, organizations) - call this separately after login
    public function loadAppData(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Load app data with full relationships
            $user = $this->getDataApplications($user);

            return response()->json([
                'result' => true,
                'data' => $user,
                'message' => 'Data aplikasi berhasil dimuat',
            ]);
        } catch (\Throwable $th) {
            Log::error('Load App Data Error: ' . $th->getMessage());
            return response()->json([
                'result' => false,
                'message' => 'Gagal memuat data aplikasi',
            ], 500);
        }
    }
}
