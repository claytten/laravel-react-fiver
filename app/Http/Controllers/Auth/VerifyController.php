<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyController extends Controller
{
    use ResponseApiTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected UserService $service
    )
    {
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(string $id): JsonResponse
    {
        $user = $this->service->findUserById($id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return $this->sendResponse([], 'Email successfully verified.');
    }

    /**
     * Resend the email verification notification.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {

        $user = $request->user();

        if (!$user) {
            return $this->sendError('User not found.', [], Response::HTTP_NOT_FOUND);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->sendResponse([], 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        return $this->sendResponse([], 'Verification link resent.');
    }
}
