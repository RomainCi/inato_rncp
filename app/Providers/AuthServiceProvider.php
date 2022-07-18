<?php

namespace App\Providers;

use App\Models\Invitation;
use App\Models\Projet;
use App\Models\RoleProjet;
use App\Models\User;
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

        Gate::define('invitation-user', function (User $user, int $id) {
            return $user->guestInvitation->where('status', 'pending')->contains('projet_id', $id);
        });
    }
}
