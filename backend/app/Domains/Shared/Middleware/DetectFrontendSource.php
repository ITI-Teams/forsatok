<?php

namespace App\Domains\Shared\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;

class DetectFrontendSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function __construct(protected FrontendUrlService $urlService) {}
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check for new dashboard routes (React Dashboard)
        if ($request->is('api/dashboard/*') || $request->is('dashboard/*')) {
            $this->urlService->setSource('react_dashboard');
        }
        // 2. Check header (for candidates shared between React and Angular)
        elseif ($request->header('App-Source')) {
            $this->urlService->setSource($request->header('App-Source'));
        }
        // 3. Other API routes (can be considered React by default or left as is)
        elseif ($request->is('api/*')) {
            // $this->urlService->setSource('react');
        }

        return $next($request);
    }
}
