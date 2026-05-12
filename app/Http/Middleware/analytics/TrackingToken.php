<?php

namespace App\Http\Middleware\analytics;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TrackingToken
{
    public function handle(Request $request, Closure $next): HttpResponse
    {
        $token = (string) $request->header('X-Tracking-Token', '');
        $requireToken = (bool) config('analytics.tracking.require_token', false);
        $defaultRole = (string) config('analytics.tracking.default_role', 'public');
        $tokens = (array) config('analytics.tracking.tokens', []);

        if ($token === '') {
            if ($requireToken) {
                return Response::api(null, 'Unauthenticated', 401);
            }

            $role = $defaultRole;
        } else {
            $role = $tokens[$token] ?? null;

            if (! is_string($role) || $role === '') {
                return Response::api(null, 'Unauthenticated', 401);
            }
        }

        if (! config()->has("analytics.tracking.roles.$role")) {
            return Response::api(null, 'Forbidden', 403);
        }

        $request->attributes->set('tracking_role', $role);

        return $next($request);
    }
}
