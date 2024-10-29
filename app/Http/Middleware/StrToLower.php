<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StrToLower
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->toLowerCase($request, 'word');
        $this->toLowerCase($request, 'letter');

        return $next($request);
    }

    private function toLowerCase(Request $request, string $subjectKey): void
    {
        if($request->has($subjectKey)) {
            $word = $request->input($subjectKey);

            if($word === null) {
                return;
            }

            $request->replace([
                $subjectKey => strtolower($word),
            ]);
        }
    }
}
