<?php

namespace App\Exceptions;

use App\Traits\ResponseApiTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // this response view is just example
        // you can change it to anything you want to redirect another front end
        return $request->is() 
                ? $this->sendError('Unauthorized', [], 401)
                : response()->view('errors.401', [], 401);
    }

    public function render($request, Throwable $exception)
    {
            if($exception instanceOf ModelNotFoundException) {
                return $this->sendError('Model not found', [], 404);
            } 

            if ($exception instanceOf NotFoundHttpException) {
                return $this->sendError('Incorrect route', [], 404);
            }

        return parent::render($request, $exception);

    }
}
