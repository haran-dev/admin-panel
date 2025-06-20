<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class verfiedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $subjectText;
    public $messageText;
    public $userName; 

    public function __construct($link, $subjectText, $messageText, $userName)
    {
        $this->link = $link;
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
            ->view('email.email_verification')
            ->with([
                'link' => $this->link,
                'messageText' => $this->messageText,
                'userName' => $this->userName,
            ]);
    }
}
