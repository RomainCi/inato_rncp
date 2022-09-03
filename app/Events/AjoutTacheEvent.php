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

class AjoutTacheEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public int $id;
    public object $taches;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $taches)
    {
        $this->id = $id;
        $this->taches = $taches;
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
        return 'ajout-tache';
    }
    public function broadcastWith()
    {
        return [
            "hello" => "wordl",
            'message' => $this->id,
            'from' => auth()->user()->nom,
            "tache" => $this->taches
        ];
    }
}
