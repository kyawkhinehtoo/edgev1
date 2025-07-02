<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class DynamicMailService
{
    protected $smtp;

    public function __construct()
    {
        // Load SMTP settings from DB (you can extend this to load by tenant or user)
        $this->smtp = SmtpSetting::firstOrFail();
    }

    protected function setMailConfig(): void
    {
       // dd($this->smtp);
        Config::set('mail.default', 'dynamic');

        Config::set('mail.mailers.dynamic', [
            'transport' => 'smtp',
            'host' => $this->smtp->host,
            'port' => $this->smtp->port,
            'encryption' => $this->smtp->encryption,
            'username' => $this->smtp->username,
            'password' => $this->smtp->password,
        ]);

        Config::set('mail.from.address', $this->smtp->from_address);
        Config::set('mail.from.name', $this->smtp->from_name);
    }

    public function send(string|array $to, Mailable $mailable): void
    {
        $this->setMailConfig();

         try {
            Mail::mailer('dynamic')->to($to)->send($mailable);
        } catch (\Exception $e) {
            \Log::error('Mail send failed: ' . $e->getMessage());
            throw $e; // or handle gracefully
        }
    }

    public function queue(string|array $to, Mailable $mailable): void
    {
        $this->setMailConfig();

        Mail::mailer('dynamic')->to($to)->queue($mailable);
    }
}