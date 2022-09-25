<?php

namespace App\Providers;

use App\Models\Invitation;
use App\Models\Lists;
use App\Models\Projet;
use App\Models\RoleProjet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use phpDocumentor\Reflection\Types\Boolean;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-projet', function (User $user, int $id): bool {
            return $user->roleProjets->where('role', 'admin')->contains('projet_id', $id);
        });
        Gate::define('verification-invitation', function (User $user, int $id, Projet $projet) {
            return $user->invitation->where('user_id', $id)->contains('projet_id', $projet->id);
        });

        Gate::define('invitation-user', function (User $user, int $id, int $idInvit, int $idAdmin) {
            return $user->guestInvitation->where('status', 'pending')
                ->where('id', $idInvit)
                ->where('admin_id', $idAdmin)
                ->contains('projet_id', $id);
        });
        Gate::define('editeur-projet', function (User $user, int $id): bool {
            return $user->editeurProjet->contains('projet_id', $id);
        });
        Gate::define('acces-lists', function (User $user, int $id): bool {
            return $user->roleProjets->contains('projet_id', $id);
        });
        Gate::define('move-item', function (User $user, Model $liste): bool {
            return $liste->listRole->contains('user_id', $user->id);
        });
    }
}
