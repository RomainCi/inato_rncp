<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class InvitationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public int $id;
    public string $status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $status)
    {
        $this->id = $id;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('invitation' . $this->id);
    }

    public function broadcastAs()
    {
        return 'invitation';
    }
    public function broadcastWith()
    {
        return [
            'message' => "succes",
            "status" => $this->status,
            "idInvit" => $this->id
        ];
    }
}
