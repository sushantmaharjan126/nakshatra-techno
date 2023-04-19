<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->is('dashboard'));
        $response = $next($request);
        $response->header('Http-Header-Custom', 'Site is Under Maintenance.');
        
        if(Auth::check()) {
            
            return $response;
        }

        if (!Auth::check() && app()->isDownForMaintenance()) {

            $log = [
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'CLIENT_IP' => $request->ip(),
                'TIME' => now()->format('Y-m-d H:i A')
                // 'TIME' => microtime(true)
            ];

            // dd($log);

            Log::info(json_encode($log));
            
            return response()->view('maintenance-mode', [], 503);
        }
        
        return $response;
    }
}
