<?php

namespace App\Domains\Shared\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check Approved Status Middleware
 * 
 * Ensures that users (especially employers) have been approved by admin
 * before they can access protected routes in the dashboard.
 * Also prevents candidates from accessing the dashboard.
 */
class CheckApprovedStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // If no user is authenticated, let auth middleware handle it
        if (!$user) {
            return redirect('login');
        }
        
        // Block candidates from accessing dashboard
        if ($user->type === 'candidate') {
            Auth::logout();
            return redirect('login')->with('error', 'This dashboard is for admins and employers only. Candidates cannot access this area.');
        }
        
        // Check if user is banned
        if ($user->status === 'banned') {
            Auth::logout();
            return redirect('login')->with('error', 'Your account has been banned. Please contact support.');
        }
        
        // Check employer approval status (admins are auto-approved)
        if ($user->type === 'employer' && $user->status !== 'approved') {
            Auth::logout();
            
            $message = match ($user->status) {
                'pending' => 'Your account is pending admin approval. Please wait for approval before logging in.',
                'rejected' => 'Your account has been rejected. Please contact support for more information.',
                default => 'Your account status does not allow access. Please contact support.',
            };
            
            return redirect('login')->with('error', $message);
        }
        
        return $next($request);
    }
}
