<?php

use App\Models\Invitation;
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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('projet{id}', function ($user, int $id) {

    $response = RoleProjet::where('user_id', $user->id)->where('projet_id', $id)->exists();
    if ($response === true) {
        $projet = Projet::find($id);
        return ['userId' => $user->id, 'name' => $user->nom, 'projetId' => $id, 'nomProjet' => $projet->nom];
    } else {
        return false;
    }
});

// Broadcast::channel('invitation{id}', function ($user, int $id) {
//     $invit = Invitation::find($id);
//     if ($invit->admin_id === $user->id || $invit->user_id === $user->id) {
//         return true;
//     } else {
//         return false;
//     }
// });
