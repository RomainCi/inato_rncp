<?php

use App\Models\Projet;
use App\Models\RoleProjet;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('projet{id}', function ($user, $id) {

    $response = RoleProjet::where('user_id', $user->id)->where('projet_id', $id)->exists();
    if ($response === true) {
        $projet = Projet::find($id);
        return ['userId' => $user->id, 'name' => $user->nom, 'projetId' => $id, 'nomProjet' => $projet->nom];
    } else {
        return false;
    }
});
