<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyBaselineData
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user->baselineData()->exists()) {
            return redirect()->route('baseline.create')
                ->with('warning', 'Please complete your baseline data first.');
        }

        return $next($request);
    }
}
