<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.auth'), statusCode: 401);
            }
        });

        $this->renderable(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.401'), statusCode: 401);
            }
        });

        $this->renderable(function (BadRequestException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.402'), statusCode: 402);
            }
        });

        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.403'), statusCode: 403);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.404'), statusCode: 404);
            }
        });

        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.404'), statusCode: 404);
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.405'), statusCode: 405);
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $this->renderable(function (HttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->error(message: __('errors.503'), statusCode: 503);
            }
        });
    }
}
