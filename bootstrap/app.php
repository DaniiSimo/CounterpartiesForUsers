<?php

use Illuminate\Foundation\{Application, Configuration\Exceptions, Configuration\Middleware};
use App\Exceptions\CounterpartyUniqueException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

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
    })->create();
