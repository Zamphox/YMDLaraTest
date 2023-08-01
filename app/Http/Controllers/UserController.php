<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService\UserServiceInterface as UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return JsonResponse
     */
    public function getCompanies(): JsonResponse
    {
        return response()->json($this->userService->getCompanies());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addCompany(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|string|max:256',
            'phone' => 'required|string|max:36',
            'description' => 'required|string|max:2048',
        ]);

        $this->userService->addCompany($request->all());

        return response()->json([], 201);
    }
}
