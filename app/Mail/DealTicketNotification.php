<?php

namespace App\Mail;

use App\Models\DealTicket;
use App\Models\TransportTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealTicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The deal ticket instance.
     *
     * @var \App\Models\DealTicket
     */
    public DealTicket $dealTicket;

    /**
     * The transport transaction instance.
     *
     * @var \App\Models\TransportTransaction
     */
    public TransportTransaction $transaction;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\DealTicket $dealTicket
     * @param \App\Models\TransportTransaction $transaction
     */
    public function __construct(DealTicket $dealTicket, TransportTransaction $transaction)
    {
        $this->dealTicket = $dealTicket;
        $this->transaction = $transaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deal Ticket Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'dealticket.notification',
            with: [
                'dealTicket' => $this->dealTicket,
                'transaction' => $this->transaction,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
