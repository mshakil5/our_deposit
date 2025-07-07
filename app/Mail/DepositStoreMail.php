<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositStoreMail extends Mailable
{
    use Queueable, SerializesModels;
    public $array;

    /**
     * Create a new message instance.
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->from('info@pmk.kmushakil.com', 'PMK')
        ->subject('Installment store confirmation')
        ->attach($this->array['file'],['mime'=>'application/pdf'])
        ->markdown('mail.deposit');
    }
}
