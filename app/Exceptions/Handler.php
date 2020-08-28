<?php

namespace App\Exceptions;

use App\Log;
use App\Notifications\DiscordNotification;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        $eMessage = $exception->getMessage();
        $ignore = [
            "Illuminate\\Auth\\AuthenticationException",
            "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
            "Symfony\\Component\\HttpKernel\\Exception\\HttpException",
            "Illuminate\\Session\\TokenMismatchException",
            "Illuminate\\Database\\Eloquent\\ModelNotFoundException",
            "Illuminate\\Validation\\ValidationException",
            "NotificationChannels\\Discord\\Exceptions\\CouldNotSendNotification",
        ];

        try {
            if (!in_array(get_class($exception), $ignore) && $eMessage != '') {
                if (config('services.discord.active') === 'ignore' OR config('services.discord.active') === true && config('app.debug') === false) {
                    Notification::send(new Log(), new DiscordNotification('exception', null, $exception));
                }
            }
        } catch (Exception $ex) {
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
