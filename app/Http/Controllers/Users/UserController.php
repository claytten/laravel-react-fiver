<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\UserService;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Traits\StoreImage;

class UserController extends Controller
{
    use ResponseApiTrait, StoreImage;

    public function __construct(
        protected UserService $service
    )
    {
    }

    /**
     * Update the password for the user.
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->input('old_password'), $user->password)) {
            return $this->sendError('The provided credentials are incorrect.', [], Response::HTTP_UNAUTHORIZED);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        $user->tokens()->delete();
        $user->sendReceiveResetPasswordNotification();

        return $this->sendResponse([], 'Password updated successfully and you are logged out all devices.');
    }

    /**
     * Update the profile for the user.
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->sendResponse($user->refresh(), 'Profile updated successfully.');
    }

    /**
     * Update the avatar for the user.
     *
     * @param UpdateAvatarRequest $request
     * @return JsonResponse
     */
    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $user = $this->service->saveImage($request->user(), $request->file('avatar'), 'public');

        return $this->sendResponse($user, 'Avatar updated successfully.');
    }

    /**
     * Delete the avatar for the user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->avatar_url) {
            return $this->sendError('Avatar not found.', [], Response::HTTP_NOT_FOUND);
        }

        $user->avatar_url = $this->deleteImage('public', $user->getRawOriginal('avatar_url')) ? null : $user->getRawOriginal('avatar_url');
        $user->save();

        return $this->sendResponse($user->refresh(), 'Avatar deleted successfully.');
    }
}
