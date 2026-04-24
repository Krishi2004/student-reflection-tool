<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use URL;

class ReflectionVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $reflection;
    public $verification_url;
    public function __construct($reflection)
    {
        $this->reflection = $reflection;

        $assessment = $reflection->skillAssessments()->first(); // gets the record in the skill assessment table



        $this->verification_url = route('reflection.review', [ // generates the link that the supervisor will click
            'id' => $reflection->id,
            'verification_token' => $assessment->verification_token,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reflection Verification', // subject name of the email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reflections.verification', // the view file for the email
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
