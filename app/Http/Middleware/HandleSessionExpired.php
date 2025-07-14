<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\TokenMismatchException;

class HandleSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Clear remember me cookies when session expires
            $this->clearRememberMeCookies();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please login again.',
                    'redirect' => route('login')
                ], 419);
            }
            
            return redirect()->route('login')
                ->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
        } catch (\Exception $e) {
            // Handle other session-related exceptions
            if (str_contains($e->getMessage(), 'session') || str_contains($e->getMessage(), 'expired')) {
                $this->clearRememberMeCookies();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session error. Please login again.',
                        'redirect' => route('login')
                    ], 419);
                }
                
                return redirect()->route('login')
                    ->with('error', 'Terjadi masalah dengan session. Silakan login kembali.');
            }
            
            throw $e;
        }
    }
    
    /**
     * Clear remember me cookies
     */
    private function clearRememberMeCookies()
    {
        cookie()->queue(cookie()->forget('remember_username'));
        cookie()->queue(cookie()->forget('remember_password'));
    }
}
