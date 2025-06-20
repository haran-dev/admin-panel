<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class emailSending extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $subjectText;
    public $messageText;
    public $userName; 

    public function __construct($subjectText, $messageText, $userName)
    {
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
            ->view('email.email_sending')
            ->with([
                'messageText' => $this->messageText,
                'userName' => $this->userName,
            ]);
    }
}
