<?php

namespace App\Notifications;

use App\Models\Bundle;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewBundleNotification extends Notification
{
    use Queueable;

    public $bundle;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Bundle $bundle)
    {
        $this->bundle = $bundle;
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
        return (new WebPushMessage)
            ->title('Online Gifting - New Bundle Added ')
            ->icon('/icon-og.png')
            ->body($this->bundle->name . '(Rp.' . $this->bundle->calculatePrice() . ')')
            ->action('View bundle', 'view_bundle|' . $this->bundle->id)
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
