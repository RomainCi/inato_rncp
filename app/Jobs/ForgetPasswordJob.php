<?php

namespace App\Jobs;

use App\Mail\ForgetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ForgetPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected string $email;
    protected string $token;
    protected string $password;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $token, $password)
    {
        $this->email = $email;
        $this->token = $token;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new ForgetPasswordMail($this->token, $this->password));
    }
}
