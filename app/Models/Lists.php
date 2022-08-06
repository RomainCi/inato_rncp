<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lists extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function listProjet()
    {
        return $this->belongsTo(Projet::class, 'projet_id', 'id');
    }

    public function listTaches()
    {
        return $this->hasMany(Taches::class, 'list_id', 'id')->orderBy('index');
    }

    public function listUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function listRole(): HasMany
    {
        return $this->hasMany(RoleProjet::class, "projet_id", "projet_id")->where('role', 'admin')->orWhere('role', 'editeur');
    }
}
