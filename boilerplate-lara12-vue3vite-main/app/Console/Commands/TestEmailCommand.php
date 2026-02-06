<?php

namespace App\Console\Commands;

use App\Mail\EmailVerification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : The email address to send to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test verification email to check if email configuration is working';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Email Configuration...');
        $this->newLine();

        // Display current mail configuration
        $this->table(
            ['Configuration', 'Value'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_USERNAME', config('mail.mailers.smtp.username')],
                ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
                ['APP_ENV', config('app.env')],
            ]
        );

        $this->newLine();

        // Get recipient email
        $recipientEmail = $this->argument('email') ?? '211232638@uii.ac.id';

        if (!$this->argument('email')) {
            $this->info("No email provided, using default test email: {$recipientEmail}");
        }

        // Check if mail driver is 'log'
        if (config('mail.default') === 'log') {
            $this->warn('⚠️  MAIL_MAILER is set to "log" - emails will be written to logs instead of sent!');
            $this->warn('   To send real emails, update your .env file:');
            $this->warn('   MAIL_MAILER=smtp');
            $this->newLine();
        }

        // Generate test OTP
        $testOtp = rand(100000, 999999);
        $testUrl = config('app.url').'/app/verify-email?token='.$testOtp.'&email='.urlencode($recipientEmail);

        $this->info("📧 Sending test email to: {$recipientEmail}");
        $this->info("🔐 Test OTP Code: {$testOtp}");

        try {
            // Send test email
            Mail::to($recipientEmail)->send(new EmailVerification($testOtp, $testUrl, 'Test User'));

            $this->newLine();
            $this->info('✅ Email sent successfully!');
            $this->newLine();

            if (config('mail.default') === 'log') {
                $this->info('📝 Check the email content in:');
                $this->info('   storage/logs/laravel.log');
            } else {
                $this->info('📬 Check the inbox of: '.$recipientEmail);
            }

            $this->newLine();
            $this->info('🔍 Additional checks:');
            $this->info('   1. Check Laravel logs: tail -f storage/logs/laravel.log');
            $this->info('   2. Check email provider dashboard (if using SMTP)');
            $this->info('   3. Check spam folder in recipient inbox');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to send email!');
            $this->error('Error: '.$e->getMessage());
            $this->newLine();
            $this->warn('Troubleshooting tips:');
            $this->warn('1. Verify SMTP credentials in .env file');
            $this->warn('2. Check if SMTP server is accessible');
            $this->warn('3. Verify firewall/network settings');
            $this->warn('4. Check Laravel logs: storage/logs/laravel.log');

            return Command::FAILURE;
        }
    }
}
