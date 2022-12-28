<?php

namespace Scriptologia\Chat\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
//use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $data;
//    protected $chat_id ;
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
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PresenceChannel('chat.to-user-'. $this->data['message']['to']),
            new PresenceChannel('chat.to-user-'. $this->data['message']['from'])
        ];
    }

    public function broadcastWith()
    {
        return ['user' => auth()->user(), 'message' => $this->data['message'], 'chat_id' => $this->data['chat_id']];
    }
}
