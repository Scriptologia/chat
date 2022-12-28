<?php

namespace Scriptologia\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeletedMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return Channel|Channel[]|PresenceChannel|string|string[]
     */
    public function broadcastOn()
    {
        return [
            new PresenceChannel('chat.to-user-'. $this->data['user_id'])
        ];
    }

    public function broadcastWith()
    {

        return $this->data;
    }
}
