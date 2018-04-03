<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword2 extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage)
            ->subject('Mapnv - réinitialisation de mot de passe')
            ->greeting('Bonjour,')
            ->line('Vous recevez cet email parce que nous avons reçu une requête de réinitialisation de mot de passe provenant de votre compte.')
            ->action('Réinitialiser le mot de passe', url('password/reset', $this->token))
            ->line('Si vous n\'avez pas demandé une réinitialisation de votre mot de passe, ne tenez pas compte de ce message.');
    }
}
