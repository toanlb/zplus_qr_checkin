<?php

namespace App\Mail;

use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipExpiringNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Đối tượng User
     */
    public $user;

    /**
     * Đối tượng Membership
     */
    public $membership;

    /**
     * Số ngày còn lại
     */
    public $daysLeft;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Membership $membership)
    {
        $this->user = $user;
        $this->membership = $membership;
        $this->daysLeft = Carbon::now()->diffInDays($membership->end_date);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo: Thành viên của bạn sắp hết hạn',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.membership-expiring',
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
