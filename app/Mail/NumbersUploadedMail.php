<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;


class NumbersUploadedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $csvFilePath;
    public $csvFileName;

    /**
     * Create a new message instance.
     */
    public function __construct($csvFilePath, $csvFileName)
    {
        $this->csvFilePath = $csvFilePath;
        $this->csvFileName = $csvFileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Numbers Uploaded Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.numbers_uploaded',
            // ->view('emails.numbers_uploaded') // Use the email view file

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->csvFilePath),
            // 'as' => $this->csvFileName,
            // 'mime'=>'text/csv',
        ];
    }
}
