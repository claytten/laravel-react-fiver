<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Traits\ResponseApiTrait;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends Controller
{
    use ResponseApiTrait;
    
    /**
     * Sending password reset link into email user.
     * 
     * @param App\Http\Requests\Auth\ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
            ? $this->sendResponse([], __($status))
            : $this->sendError(__($status), [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Reset password user processing.
     * it will checking signed middleware for encrypting route from email
     * then it will checking validation request
     * 
     * @param App\Http\Requests\Auth\ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password)
                ]);
                $user->save();
                $user->tokens()->delete();
                $user->sendReceiveResetPasswordNotification();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? $this->sendResponse([], __($status))
            : $this->sendError(__($status), [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
