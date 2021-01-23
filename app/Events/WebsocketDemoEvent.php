<?php

namespace App\Events;

use BeyondCode\LaravelWebSockets\WebSockets\Channels\Channel as ChannelsChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebsocketDemoEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $test;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($test)
    {
        $this->test = $test;
    }

    // public function broadcastAs()
    // {
    //     return 'demo.test';
    // }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('DemoChannel');
        return [new PrivateChannel("App.User.{$this->userId}")];
        // return new Channel('DemoChannel');
    }
}
