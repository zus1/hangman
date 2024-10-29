<?php

namespace App\Mail;

use App\Constant\RouteName;
use App\Models\Token;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private User $user,
        private Token $token,
    ){
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'no-replay@hangman.com',
            subject: 'Reset password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.reset-password',
            with: [
                'url' => route(RouteName::AUTH_RESET_PASSWORD_FORM, ['token' => $this->token->token]),
                'nickname' => $this->user->nickname,
            ],
        );
    }
}
