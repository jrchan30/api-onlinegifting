<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api/register',
        '/api/verification/*',
        '/api/password/email',
        '/api/password/reset/*',
        '/api/logout',
        '/api/payments/*',
        '/api/user-search',
        '/api/broadcasting/*',
        '/api/notifications',
        '/api/notifications/*',
        '/api/subscriptions'
    ];
}
