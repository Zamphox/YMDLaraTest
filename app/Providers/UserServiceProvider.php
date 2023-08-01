<?php

namespace App\Providers;

use App\Http\Services\UserService\UserServiceInterface;
use App\Http\Services\UserService\Default\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }
}
