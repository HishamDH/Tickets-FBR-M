<?php

namespace App\Mail;

use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PartnerReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Partner $partner;
    public array $reportData;
    public array $attachments;

    /**
     * Create a new message instance.
     */
    public function __construct(Partner $partner, array $reportData, array $attachments = [])
    {
        $this->partner = $partner;
        $this->reportData = $reportData;
        $this->attachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تقرير أداء الشريك - ' . $this->partner->name,
            from: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.partner.report',
            with: [
                'partner' => $this->partner,
                'reportData' => $this->reportData,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (isset($this->attachments['excel']) && file_exists($this->attachments['excel'])) {
            $attachments[] = Attachment::fromPath($this->attachments['excel'])
                ->as('partner_report.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        if (isset($this->attachments['pdf']) && file_exists($this->attachments['pdf'])) {
            $attachments[] = Attachment::fromPath($this->attachments['pdf'])
                ->as('partner_report.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
