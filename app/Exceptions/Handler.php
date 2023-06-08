<?php

namespace App\Exceptions;

use App\Traits\ResponseApiTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseApiTrait;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->registerErrorViewPaths();
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if($exception instanceOf ModelNotFoundException) {
            return $this->sendError('Model not found', [], Response::HTTP_NOT_FOUND);
        } 

        if ($exception instanceOf NotFoundHttpException) {
            return $this->sendError('Incorrect route', [], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceOf AuthenticationException) {
            return $request->expectsJson() 
            ? $this->sendError('Unauthorized', [], Response::HTTP_UNAUTHORIZED)
            : response()->view('errors.401', [], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceOf ValidationException) {
            return $this->sendError($exception->errors(), 'Ops! Some errors occurred', Response::HTTP_BAD_REQUEST);
        }

        return parent::render($request, $exception);

    }
}
