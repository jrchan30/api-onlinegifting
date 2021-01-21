<?php

namespace App\Notifications;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewProductNotification extends Notification
{
    use Queueable;

    public $product;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['database', 'broadcast', WebPushChannel::class];
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        // $url = url('/products/' . $this->product->id);
        $image = $this->product->images()->first();

        return (new WebPushMessage)
            ->title('Online Gifting - New Product Added')
            // ->icon('/icon-og.png')
            ->icon($image->url)
            ->body($this->product->name . ' (Rp.' . $this->product->price . ',-)')
            ->action('View product', 'view_product|' . $this->product->id)
            ->data(['id' => $notification->id]);
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
            'title' => 'New Product Added (Online Gifting)',
            'body' => $this->product->name . '(' . $this->product->price . ')',
            'action_url' => 'https://onlinegifting.shop/products/' . $this->product->id,
            'created' => Carbon::now()->toIso8601String(),
        ];
    }
}
