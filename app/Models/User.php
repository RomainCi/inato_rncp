<?php

namespace App\Models;

use App\Models\RoleProjet;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projets(): HasMany
    {
        return $this->hasMany(Projet::class, "user_id", "id")->select('id', 'nom', 'path');
    }
    public function roleProjets(): HasMany
    {
        return $this->hasMany(RoleProjet::class, "user_id", "id");
    }

    public function invitation(): HasMany
    {
        return $this->hasMany(Invitation::class, 'admin_id', 'id')
            ->where('status', 'pending')
            ->orWhere('status', 'accept');
    }

    public function guestProjets(): HasMany
    {
        return $this->hasMany(Invitation::class, "user_id", 'id')
            ->where('status', 'accept');
    }

    public function adminInvitation(): HasMany
    {
        return $this->hasMany(Invitation::class, "admin_id", "id")
            ->join('users', 'users.id', '=', 'invitations.user_id')
            ->join('projets', 'projets.id', '=', 'invitations.projet_id')
            ->select('invitations.*', 'users.prenom', 'projets.nom');
    }

    public function guestInvitation()
    {
        return $this->hasMany(Invitation::class, 'user_id', "id");
        // ->join('users', 'users.id', '=', 'invitations.admin_id')
        // ->join('projets', 'projets.id', '=', 'invitations.projet_id')
        // ->select('invitations.*', 'users.prenom', 'projets.nom');
    }
    public function test()
    {
        return $this->belongsTo(User::class);
    }
}
