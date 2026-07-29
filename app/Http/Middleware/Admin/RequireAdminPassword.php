<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireAdminPassword
{
    public function handle(Request $request, Closure $next)
    {
        $confirmedAt = time() - $request->session()->get('auth.password_confirmed_at', 0);

        if ($confirmedAt > config('auth.password_timeout', 10800)) {
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('admin.password.confirm');
        }

        return $next($request);
    }
}
