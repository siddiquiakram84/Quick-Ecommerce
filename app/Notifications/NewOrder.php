<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrder extends Notification
{
    use Queueable;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Order $order
     */
    public function __construct($order)
    {
        $this->order = $order;
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
            ->line('You have a new order! Your order details are:')
            ->line('Order ID: ' . $this->order->id)
            ->line('Total Amount: $' . $this->order->total_amount)
            ->action('View Order', url('user/orders/' . $this->order->id))
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
            'total_amount' => $this->order->total_amount,
            // You can add more data here for broadcasting or other channels
        ];
    }
}
