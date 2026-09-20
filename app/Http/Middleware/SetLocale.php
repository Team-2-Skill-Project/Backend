<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {

        // get the locale from the request header or use the default locale
        $locale = $request->header('Accept-Language', config('app.fallback_locale'));

        // validate the locale, if it's not supported, fallback to default
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = config('app.fallback_locale');
        }

        // set the application locale
        App::setLocale($locale);

        return $next($request);
    }
}
