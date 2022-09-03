<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteListeEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public int $id;
    public object $liste;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $liste)
    {
        $this->id = $id;
        $this->liste = $liste;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('projet' . $this->id);
    }
    public function broadcastAs()
    {
        return 'delete-liste';
    }
    public function broadcastWith()
    {
        return [
            "hello" => "wordl",
            'message' => $this->id,
            'from' => auth()->user()->nom,
            "listes" => $this->liste
        ];
    }
}
