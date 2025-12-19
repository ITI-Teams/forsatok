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

    public function __construct(protected FrontendUrlService $urlService)
    {
    }
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check for new dashboard routes (React Dashboard)
        if ($request->is('api/dashboard/*') || $request->is('dashboard/*')) {
            $this->urlService->setSource('react_dashboard');
        }
        // 2. Check for API routes
        elseif ($request->is('api/*')) {
            $sourceHeader = strtolower($request->header('App-Source'));

            if ($sourceHeader === 'hive') {
                $this->urlService->setSource('hive');
            } else {
                // jobhub or empty
                $this->urlService->setSource('jobhub');
            }
        }
        // 3. Default to Web/Livewire
        else {
            $this->urlService->setSource('web');
        }

        return $next($request);
    }
}
