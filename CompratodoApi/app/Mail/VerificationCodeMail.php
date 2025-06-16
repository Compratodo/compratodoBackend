<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $verificationCode;
    public $expiryTime;
    public $welcomeMessage;
    public $mainMessage;
    public $actionUrl;
    public $actionText;
    public $additionalContent;

    public function __construct($data)
    {
        $this->userName = $data['userName'];
        $this->verificationCode = $data['verificationCode'];
        $this->expiryTime = $data['expiryTime'] ?? '24 horas';
        $this->welcomeMessage = $data['welcomeMessage'] ?? null;
        $this->mainMessage = $data['mainMessage'] ?? null;
        $this->actionUrl = $data['actionUrl'] ?? null;
        $this->actionText = $data['actionText'] ?? null;
        $this->additionalContent = $data['additionalContent'] ?? null;
    }

    public function build()
    {
        return $this->subject('Código de Verificación')
                    ->view('emails.verification-code');
    }
}

