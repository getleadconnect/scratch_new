<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
		apiPrefix: 'api/user',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
		
	->withMiddleware(function (Middleware $middleware) {
        
		 $middleware->alias([
            'authware' => \App\Http\Middleware\Authware::class,
        ]);
		
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
