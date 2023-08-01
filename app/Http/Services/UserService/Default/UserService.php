<?php

namespace App\Http\Services\UserService\Default;

use App\Http\Services\UserService\UserServiceInterface;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserService implements UserServiceInterface
{
    /**
     * @param array $userData
     * @return void
     */
    public function createUser(array $userData): void
    {
        $user = new User([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'phone' => $userData['phone']
        ]);
        $user->password = Hash::make($userData['password']);

        $user->save();
    }

    /**
     * @return Collection
     */
    public function getCompanies(): Collection
    {
        return Auth::user()->companies()->get();
    }

    /**
     * @param array $companyData
     * @return void
     */
    public function addCompany(array $companyData): void
    {
        $company = new Company([
            'title' => $companyData['title'],
            'phone' => $companyData['phone'],
            'description' => $companyData['description']
        ]);
        $company->user_id = Auth::user()->id;

        $company->save();
    }

    /**
     * @param User $user
     * @param string $password
     * @return void
     */
    public function setNewPassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);

        $user->save();
    }
}
