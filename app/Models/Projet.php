<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projet extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function projetR()
    {
        return $this->hasMany(RoleProjet::class, 'projet_id', 'id');
    }

    public function projetI()
    {
        return $this->hasMany(Invitation::class, 'projet_id', 'id')
            ->join('users', 'users.id', '=', 'invitations.admin_id')
            ->join('projets', 'projets.id', '=', 'invitations.projet_id')
            ->select('invitations.*', 'users.prenom', 'projets.nom');
    }
}
