<?php

namespace App\Providers;

use App\Models\Entry;
use App\Models\User;
use App\Policies\EntryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider {
    protected $policies = [ Entry::class => EntryPolicy::class ];

    public function boot(): void {
        Gate::define('admin-only', fn(User $u) => $u->isAdmin());
    }
}
