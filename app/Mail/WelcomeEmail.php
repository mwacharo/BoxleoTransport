<?php

namespace App\Mail;

use App\Models\Rider;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $rider;
    public $password;
    public $name;
    public $email;



    /**
     * Create a new message instance.
     */
    public function __construct(Rider $rider, string $password ,$name ,$email)
    {
        $this->rider = $rider;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to Boxleo Transport')
                    ->view('emails.welcome');
    }
}
