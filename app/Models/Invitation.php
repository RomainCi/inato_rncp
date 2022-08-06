<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invitation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function admins(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id')->select('id', 'nom');
    }

    public function guests(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select('id', 'nom');
    }

    public function projets(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'projet_id', 'id')->select('id', 'nom');
    }

    // public function notifications():BelongsTo
    // {
    //     return $this->belongsTo(auth()->user())
    // }

    public function verification($adminId, $guestId, $projetId)
    {
        $invitation = Invitation::where('admin_id', $adminId)
            ->where('user_id', $guestId)
            ->where('projet_id', $projetId)
            ->where('status', 'pending')
            ->first();
        return $invitation;
    }
}
