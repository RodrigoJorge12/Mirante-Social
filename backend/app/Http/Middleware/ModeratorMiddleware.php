<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'not_authenticated'], 401);
        }
        $list = env('MODERATOR_EMAILS', '');
        $emails = array_filter(array_map('trim', explode(',', $list)));
        if (!in_array($user->email, $emails, true)) {
            return response()->json(['success' => false, 'message' => 'forbidden'], 403);
        }
        return $next($request);
    }
}
