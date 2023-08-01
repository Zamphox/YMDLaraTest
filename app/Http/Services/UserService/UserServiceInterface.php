<?php

namespace App\Http\Services\UserService;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function createUser(array $userData): void;

    public function getCompanies(): Collection;

    public function setNewPassword(User $user, string $password): void;
}
