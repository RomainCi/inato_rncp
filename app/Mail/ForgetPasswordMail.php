<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public string $token;
    public string $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $password)
    {
        $this->token = $token;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@inato.fr')
            ->subject('RESET MOT DE PASSE')
            ->view('email.resetPassword')
            ->with([
                "token" => $this->token,
                "password" => $this->password
            ]);
    }
}
