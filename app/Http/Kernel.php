<?php
namespace App\Http;


use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // ... existing global middleware
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // ... existing web middleware
        ],

        'api' => [
            // ... existing api middleware
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... existing middleware
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'auth' => \App\Http\Middleware\AuthMiddleware::class,
        'has.baseline' => \App\Http\Middleware\VerifyBaselineData::class,
    ];

}
