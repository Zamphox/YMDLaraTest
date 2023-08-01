<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService\UserServiceInterface as UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected UserService $userService;

    private string $resetSentMessage = 'The reset token has been sent to your email. Use it as a {password_reset_token: \'your token\'} parameter on the POST \'/api/user/recover-password\' request to set a new password';

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerUser(Request $request): JsonResponse
    {
        $this->validate($request, [
            'first_name' => 'required|max:36',
            'last_name' => 'required|max:36',
            'email' => 'required|email|unique:users|max:256',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'required|max:36',
        ]);

        $this->userService->createUser($request->all());

        return response()->json([], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!$token = Auth::attempt($request->only(['email', 'password']))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($token);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function recoverPassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string|exists:users'
        ]);

        $resetEmail = $request->email;
        $user = User::where('email', $resetEmail)->first();
        $resetToken = Hash::make($user->id . time());

        Mail::raw('Your reset token is: ' . $resetToken, function($message) use ($resetEmail) {
            $message->to($resetEmail)->subject('Your password reset token');
            $message->from(env('MAIL_FROM_ADDRESS','YMDLaraTest'));
        });

        $user->password_reset_token = $resetToken;
        $user->save();

        return response()->json($this->resetSentMessage);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function  resetPassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string|exists:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_reset_token' => ['required', 'string', 'exists:users']
        ], ['exists:users' => 'The password reset token is invalid']);

        $user = User::where('email', $request->email)->first();

        $this->userService->setNewPassword($user, $request->password);
        $user->password_reset_token = NULL;

        $user->save();

        return response()->json([], 204);
    }
}
