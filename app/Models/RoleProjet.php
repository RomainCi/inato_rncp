<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleProjet extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function relationRole()
    {
        return  $this->hasMany(RoleProjet::class, 'projet_id', 'id')
            ->where('user_id', '!=', auth()->user()->id);
    }

    public function userRole()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select('id', 'nom');
    }

    public function projetRole()
    {
        return $this->belongsTo(Projet::class, 'projet_id', 'id');
    }

    public function listRole()
    {
        return $this->belongsTo(Lists::class, 'projet_id', 'projet_id');
    }
}
