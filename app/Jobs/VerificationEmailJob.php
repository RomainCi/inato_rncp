<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\VerificationEmailMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class VerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $token = "";
    protected $email = "";
    protected $nom = "";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $token, $nom)
    {
        $this->email = $email;
        $this->token = $token;
        $this->nom = $nom;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new VerificationEmailMail($this->token, $this->nom));
    }
}
