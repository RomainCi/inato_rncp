<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmailMail extends Mailable
{
    use Queueable, SerializesModels;
    protected string $token = "";
    protected string $nom = "";

    /**
     * Create a new message instance.
     *
     * @return void
     * 
     */
    public function __construct($token, $nom)
    {
        $this->token = $token;
        $this->nom = $nom;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from('no-reply@inato.fr')
            ->subject('VERIFICATION EMAIL')
            ->view('email.verificationInscription')
            ->with([
                'nom' => $this->nom,
                'token' => $this->token,
            ]);
    }
}
