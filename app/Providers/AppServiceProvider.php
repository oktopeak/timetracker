<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Gate::define('is-admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('modify-team', function (User $user, Team $team) {
            return $user->id === $team->team_leader_id || $user->role === 'admin';
        });

        Gate::define('modify-task', function (User $user, Team $team) {
            return $user->id === $team->team_leader_id
                || $user->role === 'admin'
                || $team->members->contains('id', $user->id);
        });
    }
}
