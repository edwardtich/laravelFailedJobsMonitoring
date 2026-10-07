<?php

namespace EdwardTich\FailedJobsManager\Http\Middleware;

use Closure;
use EdwardTich\FailedJobsManager\Authorization\FailedJobsManagerAuthorization;
use Illuminate\Http\Request;

class AuthorizeFailedJobsManager
{
    public function __construct(private readonly FailedJobsManagerAuthorization $authorization)
    {
    }

    public function handle(Request $request, Closure $next, string $ability): mixed
    {
        abort_unless($this->authorization->canUseStandalone($request->user(), $ability), 403);

        return $next($request);
    }
}
