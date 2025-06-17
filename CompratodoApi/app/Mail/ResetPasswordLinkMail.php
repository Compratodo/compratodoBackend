<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $url)
    {
        $this->userName = $userName;
        $this->url = $url; 
    }

    public function build() {
        return $this->subject('Recuperación de contraseña')
                    ->view('emails.reset-password-link');
    }
}
