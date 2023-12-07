<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderUpdated extends Notification
{
    use Queueable;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * The message to include in the notification.
     *
     * @var string
     */
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Order $order
     * @param string $message
     */
    public function __construct($order, $message)
    {
        $this->order = $order;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your order has been updated with the following changes:')
            ->line($this->message)
            ->action('View Updated Order', url('/orders/' . $this->order->id))
            ->line('Thank you for choosing our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => $this->message,
            // You can add more data here for broadcasting or other channels
        ];
    }
}
