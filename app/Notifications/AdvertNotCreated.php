<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdvertNotCreated extends Notification
{
    use Queueable;

    protected $advert;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($advert)
    {
        $this->advert = $advert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'advert' =>  $this->advert,
            'message' => 'Something went wrong. The advert you just created wasn\'t saved to your AdWords account. We are investigating.',
        ];
    }
}
