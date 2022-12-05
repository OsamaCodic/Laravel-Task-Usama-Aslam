<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        // $this->from_email = $data['from'];
        // $this->subject = $data['subject'];
        // $this->first_name = 'xyz';

        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->from($this->from_email)
        // ->subject($this->subject)
        // ->view('emails.user-mail')
        // ->with('first_name', 'usama');

        return $this->from($this->data['from'])
        ->subject($this->data['subject'])
        ->view('emails.user-mail')
        ->with('data', $this->data);
    }
}
