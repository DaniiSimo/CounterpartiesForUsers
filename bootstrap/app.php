<?php

use Illuminate\Foundation\{Application, Configuration\Exceptions, Configuration\Middleware};
use App\Exceptions\CounterpartyUniqueException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use \App\Http\Middleware\ForceJsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup(group: 'api', middleware: ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(
            fn (CounterpartyUniqueException $e, $request) =>
            $request->is('api/*')
                ? response()->json(
                    data:
                    [
                        'message'   => $e->getMessage(),
                        'data' => [
                            'ogrns' => $e->ogrns
                        ]
                    ],
                    status: 409
                )
                : null
        );
        $exceptions->render(
            fn (ThrottleRequestsException $e, $request) =>
                $request->is('api/*')
                    ? response()->json(data: ['message'   => $e->getMessage()], status: 429)
                    : null
        );
    })->create();
