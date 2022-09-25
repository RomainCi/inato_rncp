<?php

namespace App\Events;

use App\Models\Projet;
use App\Models\RoleProjet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class RoleEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->id = $id;
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
        return 'role-projet';
    }
    public function broadcastWith()
    {
        return [
            'message' => $this->id,
            'from' => auth()->user()->nom,
        ];
    }
}
