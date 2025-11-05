<?php

namespace App\Domains\Users\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $name;

    public function __construct(string $code, string $name = '')
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function build()
    {
        $html = "
        <h2>Hello {$this->name},</h2>
        <p>Your verification code is:</p>
        <h1 style='color:#0f7fd9;'>{$this->code}</h1>
        <p>Enter this code in the app to verify your account.</p>
    ";

        return $this->subject('Your verification code')
            ->html($html);
    }
}
