<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->subject = ($invoice->status === 'QUOTED' ? 'Penawaran' : 'Faktur') . ' - ' . $invoice->invoice_number;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $invoiceType = $this->invoice->status === 'QUOTED' ? 'Penawaran' : 'Faktur';
        
        return new Content(
            view: 'emails.invoice-notification',
            with: [
                'invoice' => $this->invoice,
                'invoiceType' => $invoiceType,
                'customerName' => $this->invoice->customer_name,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        try {
            $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice])->setPaper('a4');
            $filename = "{$this->invoice->status}-{$this->invoice->invoice_number}.pdf";
            
            return [
                [
                    'data' => $pdf->output(),
                    'name' => $filename,
                    'options' => [
                        'mime' => 'application/pdf',
                    ],
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF attachment for invoice', [
                'invoice_id' => $this->invoice->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
