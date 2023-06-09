<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\UserService;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ResponseApiTrait;

    public function __construct(
        protected UserService $service
    )
    {
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $fieldType = $request->has('email') ? 'email' : 'username';
        $res = $this->service->attemptLoginUser($fieldType, $data[$fieldType], $data['password'], $request->userAgent());
        return $this->sendResponse($res, 'User login successfully.');
    }

    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = $this->service->store($data);

        $user->sendEmailVerificationNotification();

        return $this->sendResponse($user->toArray(), 'Registration successful. A verification email has been sent to your email address.');
    }

    /**
     * Logout user (Revoke the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'User logged out successfully.');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe(Request $request): JsonResponse
    {
        return $this->sendResponse($request->user(), 'User data retrieved successfully.');
    }
}
