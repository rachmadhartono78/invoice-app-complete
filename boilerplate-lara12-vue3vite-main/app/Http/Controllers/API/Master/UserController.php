<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Models\AuthorityUser;
use App\Models\AuthorityUserOrganization;
use App\Models\User;
use App\Models\UserIdentifier;
use App\Rules\UniqueUserIdentifier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

Carbon::setLocale('id');

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with(['organizations', 'authorities.application']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('registration_number', 'like', "%{$search}%");
                });
            }

            // Filter by authority
            if ($request->has('authority_code')) {
                $authorityCodes = is_array($request->query('authority_code'))
                    ? $request->query('authority_code')
                    : explode(',', $request->query('authority_code'));

                $query->whereHas('authorities', function ($query) use ($authorityCodes) {
                    $query->whereIn('code', $authorityCodes);
                });
            }

            if ($request->has('authority_id')) {
                $authorityId = $request->query('authority_id');
                $query->whereHas('authorities', function ($query) use ($authorityId) {
                    $query->where('authorities.id', $authorityId);
                });
            }

            // Filter by organization
            if ($request->has('organization_id')) {
                $organizationId = $request->query('organization_id');
                $query->whereHas('organizations', function ($query) use ($organizationId) {
                    $query->where('organizations.id', $organizationId);
                });
            }

            // Get total count before pagination
            $totalCount = $query->count();

            // Pagination
            $page = max(1, (int) $request->query('page', 1));
            $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
            $offset = ($page - 1) * $perPage;

            $users = $query->offset($offset)
                          ->limit($perPage)
                          ->orderBy('name')
                          ->get();

            // Add formatted data for display
            $users->each(function ($user) {
                $user->organizations_names = $user->organizations->pluck('name')->join(', ');
                $user->authorities_names = $user->authorities->pluck('name')->join(', ');
            });

            return response()->json([
                'data' => $users,
                'message' => 'Users fetched successfully',
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalCount,
                    'last_page' => ceil($totalCount / $perPage),
                    'from' => $totalCount > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $totalCount),
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching users: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching users.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', 'unique:users,email', new UniqueUserIdentifier('email')],
                'registration_number' => 'nullable|string|max:255',
                'phone' => ['nullable', 'string', 'max:255', new UniqueUserIdentifier('phone')],
                'organizations' => 'required|array|min:1',
                'organizations.*' => 'exists:organizations,id',
                'authorities' => 'nullable|array',
                'authorities.*.authority' => 'required|exists:authorities,id',
                'authorities.*.organizations' => 'required|array|min:1',
                'authorities.*.organizations.*' => 'exists:organizations,id',
                'identifiers' => 'nullable|array',
                'identifiers.*.type' => 'required|in:email,phone,username',
                'identifiers.*.value' => 'required|string',
            ]);

            // Validate uniqueness for each identifier
            if (isset($validatedData['identifiers'])) {
                foreach ($validatedData['identifiers'] as $index => $identifier) {
                    $validator = validator(['value' => $identifier['value']], [
                        'value' => [new UniqueUserIdentifier($identifier['type'])],
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'message' => "Identifier at index {$index}: ".$validator->errors()->first('value'),
                            'errors' => ['identifiers' => ["Index {$index}: ".$validator->errors()->first('value')]],
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }
            }

            DB::beginTransaction();

            // Create new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'registration_number' => $validatedData['registration_number'],
                'password' => bcrypt('codakartajaya'),
            ]);

            // Create user identifiers in batch if any
            if (isset($validatedData['identifiers']) && !empty($validatedData['identifiers'])) {
                $identifiersToInsert = [];
                foreach ($validatedData['identifiers'] as $identifierData) {
                    $identifiersToInsert[] = [
                        'user_id' => $user->id,
                        'type' => $identifierData['type'],
                        'value' => $identifierData['value'],
                        'verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                UserIdentifier::insert($identifiersToInsert);
            }

            // Attach organizations to the user
            $user->organizations()->attach($validatedData['organizations']);

            // Handle authorities and their organizations efficiently
            if (isset($validatedData['authorities']) && !empty($validatedData['authorities'])) {
                $authorityInserts = [];
                $authorityOrgInserts = [];

                foreach ($validatedData['authorities'] as $authorityData) {
                    // Prepare authority-user relationship data
                    $authorityUserId = DB::table('authority_users')->insertGetId([
                        'user_id' => $user->id,
                        'authority_id' => $authorityData['authority'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Prepare authority-user-organization relationships
                    foreach ($authorityData['organizations'] as $organizationId) {
                        $authorityOrgInserts[] = [
                            'id' => Str::uuid(),
                            'authority_user_id' => $authorityUserId,
                            'organization_id' => $organizationId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Batch insert authority-user-organizations
                if (!empty($authorityOrgInserts)) {
                    AuthorityUserOrganization::insert($authorityOrgInserts);
                }
            }

            DB::commit();

            // Load the user with relationships
            $user->load([
                'authorities.application',
                'authorityUsers.authority',
                'authorityUsers.organizations',
                'organizations',
                'identifiers',
            ]);

            // Transform the data to match frontend expectations
            $user->authorities->each(function ($authority) use ($user) {
                // Find the corresponding authorityUser record
                $authorityUser = $user->authorityUsers->where('authority_id', $authority->id)->first();
                if ($authorityUser) {
                    $authority->organizations = $authorityUser->organizations;
                } else {
                    $authority->organizations = collect([]);
                }
            });

            return response()->json([
                'data' => $user,
                'message' => 'User created successfully',
            ], Response::HTTP_CREATED);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        try {
            // Find user by ID with comprehensive relationships
            $user = User::with([
                'authorities' => function ($query) {
                    $query->with([
                        'organizations' => function ($q) {
                            $q->select('organizations.id', 'organizations.name', 'organizations.code', 'organizations.type', 'organizations.city');
                        },
                        'application' => function ($q) {
                            $q->select('applications.id', 'applications.name');
                        },
                        'menuAuthorities' => function ($q) {
                            $q->with([
                                'menu' => function ($mq) {
                                    $mq->select('menus.id', 'menus.name', 'menus.url', 'menus.icon');
                                },
                                'actions' => function ($actionQuery) {
                                    $actionQuery->with(['action' => function ($aq) {
                                        $aq->select('actions.id', 'actions.name', 'actions.code');
                                    }])->where('value', 1); // Only get allowed actions
                                }
                            ]);
                        }
                    ]);
                },
                'organizations' => function ($q) {
                    $q->select('organizations.id', 'organizations.name', 'organizations.code', 'organizations.type', 'organizations.city');
                },
                'identifiers' => function ($query) {
                    $query->withTrashed();
                },
                'tokens'
            ])->findOrFail($id);

            // Transform authorities data for better frontend consumption
            $user->authorities->each(function ($authority) {
                // Group menus and actions by menu
                $menuData = [];
                
                foreach ($authority->menuAuthorities as $menuAuthority) {
                    $menu = $menuAuthority->menu;
                    
                    // Skip if menu is null
                    if (!$menu) {
                        continue;
                    }
                    
                    $actions = $menuAuthority->actions->map(function ($actionUse) {
                        // Skip if action is null
                        if (!$actionUse->action) {
                            return null;
                        }
                        
                        return [
                            'id' => $actionUse->action->id,
                            'name' => $actionUse->action->name,
                            'code' => $actionUse->action->code,
                            'allowed' => $actionUse->value == 1
                        ];
                    })->filter(); // Remove null values

                    $menuData[] = [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'url' => $menu->url,
                        'icon' => $menu->icon,
                        'actions' => $actions
                    ];
                }

                $authority->menus_with_actions = $menuData;
                
                // Clean up relations to avoid redundant data
                unset($authority->menuAuthorities);
            });

            // Map active tokens / devices
            $devices = $user->tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'device_name' => $token->name,
                    'last_used_at' => $this->getTokenStatus($token, 5),
                    'created_at' => $token->created_at ? $token->created_at->toDateTimeString() : null,
                    'expires_at' => $token->expires_at ? $token->expires_at->diffForHumans() : null,
                ];
            });

            // Group authorities by application for better display (with null check)
            $authoritiesByApplication = $user->authorities->groupBy(function ($authority) {
                return $authority->application ? $authority->application->name : 'Unknown Application';
            });

            // Load authority organizations for the user
            $user->load(['authorityUsers.authority', 'authorityUsers.organizations']);
            
            // Transform the data to match frontend expectations
            $user->authorities->each(function ($authority) use ($user) {
                // Find the corresponding authorityUser record
                $authorityUser = $user->authorityUsers->where('authority_id', $authority->id)->first();
                
                if ($authorityUser) {
                    // Clear and set the organizations relationship properly
                    $authority->setRelation('organizations', $authorityUser->organizations);
                } else {
                    $authority->setRelation('organizations', collect([]));
                }
            });

            return response()->json([
                'data' => $user,
                'devices' => $devices,
                'authorities_by_application' => $authoritiesByApplication,
                'summary' => [
                    'total_authorities' => $user->authorities->count(),
                    'total_organizations' => $user->organizations->count(),
                    'total_menus' => $user->authorities->sum(function ($authority) {
                        return count($authority->menus_with_actions ?? []);
                    }),
                    'total_actions' => $user->authorities->sum(function ($authority) {
                        return collect($authority->menus_with_actions ?? [])->sum(function ($menu) {
                            return count($menu['actions'] ?? []);
                        });
                    }),
                ],
                'message' => 'User fetched successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching user: '.$e->getMessage(), [
                'user_id' => $id,
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Check if it's a model not found exception
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'message' => 'User not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'An error occurred while fetching the user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);

            // Handle JSON string for identifiers (from FormData when empty array)
            $identifiers = $request->input('identifiers');
            if (is_string($identifiers)) {
                $identifiers = json_decode($identifiers, true) ?? [];
                $request->merge(['identifiers' => $identifiers]);
            }

            // Validate request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', 'unique:users,email,'.$id, new UniqueUserIdentifier('email', $id)],
                'phone' => ['nullable', 'string', 'max:255', new UniqueUserIdentifier('phone', $id)],
                'registration_number' => 'nullable|string|max:255',
                'organizations' => 'required|array|min:1',
                'organizations.*' => 'exists:organizations,id',
                'authorities' => 'nullable|array',
                'authorities.*.authority' => 'required|exists:authorities,id',
                'authorities.*.organizations' => 'required|array|min:1',
                'authorities.*.organizations.*' => 'exists:organizations,id',
                'password' => 'nullable|string',
                'identifiers' => 'nullable|array',
                'identifiers.*.id' => 'nullable|exists:user_identifiers,id',
                'identifiers.*.type' => 'required|in:email,phone,username',
                'identifiers.*.value' => 'required|string',
            ]);

            // Validate uniqueness for each identifier
            if (isset($validatedData['identifiers']) && !empty($validatedData['identifiers'])) {
                foreach ($validatedData['identifiers'] as $index => $identifier) {
                    $identifierId = $identifier['id'] ?? null;
                    $validator = validator(['value' => $identifier['value']], [
                        'value' => [new UniqueUserIdentifier($identifier['type'], $id, $identifierId)],
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'message' => "Identifier at index {$index}: ".$validator->errors()->first('value'),
                            'errors' => ['identifiers' => ["Index {$index}: ".$validator->errors()->first('value')]],
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }
            }

            DB::beginTransaction();

            // Update user fields
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'registration_number' => $validatedData['registration_number'],
            ];

            if (isset($validatedData['password']) && !empty($validatedData['password'])) {
                $updateData['password'] = bcrypt($validatedData['password']);
            }

            $user->update($updateData);

            // Handle user identifiers efficiently
            if (array_key_exists('identifiers', $validatedData)) {
                $existingIds = [];
                $identifiersToInsert = [];

                if (!empty($validatedData['identifiers'])) {
                    foreach ($validatedData['identifiers'] as $identifierData) {
                        if (isset($identifierData['id'])) {
                            // Update existing identifier
                            UserIdentifier::where('id', $identifierData['id'])
                                ->where('user_id', $user->id)
                                ->update(['value' => $identifierData['value']]);
                            $existingIds[] = $identifierData['id'];
                        } else {
                            // Prepare new identifier for batch insert
                            $identifiersToInsert[] = [
                                'user_id' => $user->id,
                                'type' => $identifierData['type'],
                                'value' => $identifierData['value'],
                                'verified_at' => now(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }

                // Batch insert new identifiers
                if (!empty($identifiersToInsert)) {
                    UserIdentifier::insert($identifiersToInsert);
                }

                // Delete removed identifiers
                $deleteQuery = UserIdentifier::where('user_id', $user->id);
                if (!empty($existingIds)) {
                    $deleteQuery->whereNotIn('id', $existingIds);
                }
                $deleteQuery->delete();
            }

            // Sync user organizations efficiently
            $user->organizations()->sync($validatedData['organizations']);

            // Handle authorities and their organizations efficiently
            // First, get current authority-user relationships
            $currentAuthorityUsers = AuthorityUser::where('user_id', $user->id)->get();
            $currentAuthorityIds = $currentAuthorityUsers->pluck('authority_id')->toArray();
            
            $newAuthorityIds = [];
            $authorityOrgInserts = [];

            if (isset($validatedData['authorities']) && !empty($validatedData['authorities'])) {
                foreach ($validatedData['authorities'] as $authorityData) {
                    $newAuthorityIds[] = $authorityData['authority'];
                    
                    // Find or create authority-user relationship
                    $authorityUser = AuthorityUser::firstOrCreate([
                        'user_id' => $user->id,
                        'authority_id' => $authorityData['authority'],
                    ]);

                    // Clear existing authority-user-organization relationships for this authority
                    AuthorityUserOrganization::where('authority_user_id', $authorityUser->id)->delete();

                    // Prepare new authority-user-organization relationships
                    foreach ($authorityData['organizations'] as $organizationId) {
                        $authorityOrgInserts[] = [
                            'id' => Str::uuid(),
                            'authority_user_id' => $authorityUser->id,
                            'organization_id' => $organizationId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Batch insert authority-user-organizations
                if (!empty($authorityOrgInserts)) {
                    AuthorityUserOrganization::insert($authorityOrgInserts);
                }
            }

            // Remove authority-user relationships that are no longer needed
            $authoritiesToRemove = array_diff($currentAuthorityIds, $newAuthorityIds);
            if (!empty($authoritiesToRemove)) {
                $authorityUsersToRemove = AuthorityUser::where('user_id', $user->id)
                    ->whereIn('authority_id', $authoritiesToRemove)
                    ->get();

                foreach ($authorityUsersToRemove as $authorityUser) {
                    // Delete related authority-user-organizations
                    AuthorityUserOrganization::where('authority_user_id', $authorityUser->id)->delete();
                    // Delete authority-user relationship
                    $authorityUser->delete();
                }
            }

            DB::commit();

            // Load the user with relationships
            $user->load([
                'authorities.application',
                'authorityUsers.authority',
                'authorityUsers.organizations',
                'organizations',
                'identifiers',
            ]);

            // Transform the data to match frontend expectations
            $user->authorities->each(function ($authority) use ($user) {
                // Find the corresponding authorityUser record
                $authorityUser = $user->authorityUsers->where('authority_id', $authority->id)->first();
                if ($authorityUser) {
                    $authority->organizations = $authorityUser->organizations;
                } else {
                    $authority->organizations = collect([]);
                }
            });

            return response()->json([
                'data' => $user,
                'message' => 'User updated successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: '.$e->getMessage(), [
                'user_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the user.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changePassword(Request $request, string $id)
    {
        try {
            // Validate request
            $validatedData = $request->validate([
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            // Find user
            $user = User::findOrFail($id);

            // Update password
            $user->update([
                'password' => bcrypt($validatedData['new_password']),
            ]);

            return response()->json([
                'message' => 'Password reset successfully.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error resetting password: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while resetting the password.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changePhoneNumber(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'new_phone' => 'required|string|max:15|unique:users,phone,'.$id,
        ]);

        try {
            $user = User::findOrFail($id);
            $user->update([
                'phone' => $validatedData['new_phone'],
            ]);

            return response()->json([
                'message' => 'Phone number updated successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            Log::error('Error updating phone number: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating phone number.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function revokeToken(Request $request, $userId, $tokenId)
    {
        $authUser = $request->user();

        // Pastikan user ID yang direvoke sama dengan user yang login
        if ($authUser->id != $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        // Cari token user berdasarkan token_id
        $token = $authUser->tokens()->where('id', $tokenId)->first();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found or already revoked.',
            ], 404);
        }

        // Hapus token
        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully.',
        ]);
    }

    public function getTokenStatus($token, $thresholdMinutes = 5)
    {
        if (! $token) {
            return 'offline';
        }

        // Pakai last_used_at kalau ada, kalau tidak pakai created_at
        $lastActivity = $token->last_used_at ?? $token->created_at;

        if ($lastActivity->diffInMinutes(now()) <= $thresholdMinutes) {
            return 'Online';
        }

        return $lastActivity->diffForHumans(); // Misal "6 menit yang lalu", "2 jam yang lalu"
    }
}
