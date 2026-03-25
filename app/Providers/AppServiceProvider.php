<?php

namespace App\Providers;

use App\Models\Pendaftaran;
use App\Observers\PendaftaranObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        \Carbon\Carbon::setLocale('id');
        Pendaftaran::observe(PendaftaranObserver::class);
    }
}
