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

class UpdateTitreTacheEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public int $id;
    public object $tache;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $tache)
    {
        $this->id = $id;
        $this->tache = $tache;
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
        return 'update-tache';
    }
    public function broadcastWith()
    {
        return [
            "hello" => "wordl",
            'message' => $this->id,
            'from' => auth()->user()->nom,
            "tache" => $this->tache
        ];
    }
}
