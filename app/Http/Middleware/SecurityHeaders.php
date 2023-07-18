<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds the security headers to every response, including a nonce-based
 * Content-Security-Policy. Inline scripts must carry nonce="{{ Vite::cspNonce() }}".
 */
class SecurityHeaders
{
    private const VERIFF = ['https://*.veriff.com', 'https://*.veriff.me'];

    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Str::random(40);
        Vite::useCspNonce($nonce);

        $response = $next($request);

        $headers = [
            'Content-Security-Policy' => $this->contentSecurityPolicy($nonce),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            // The camera is only needed by the Veriff identity check frame
            'Permissions-Policy' => 'camera=(self "https://*.veriff.com" "https://*.veriff.me"), microphone=(), geolocation=(), payment=(), usb=()',
        ];

        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            if (!$response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        $response->headers->remove('X-Powered-By');

        return $response;
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        $dev = $this->viteDevServer();
        $veriff = implode(' ', self::VERIFF);

        $directives = [
            'default-src' => "'self'",
            'script-src' => trim("'self' 'nonce-{$nonce}' https://cdn.veriff.me {$dev['http']}"),
            'style-src' => trim("'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net {$dev['http']}"),
            'font-src' => "'self' data: https://fonts.gstatic.com",
            'img-src' => trim("'self' data: blob: https://cdn.jsdelivr.net {$dev['http']}"),
            'connect-src' => trim("'self' {$veriff} {$dev['http']} {$dev['ws']}"),
            'frame-src' => $veriff,
            'frame-ancestors' => "'none'",
            'form-action' => "'self'",
            'base-uri' => "'self'",
            'object-src' => "'none'",
        ];

        $policy = collect($directives)->map(fn ($value, $key) => "{$key} {$value}")->implode('; ');

        return app()->isProduction() ? $policy . '; upgrade-insecure-requests' : $policy;
    }

    /** Origins of the Vite dev server while `npm run dev` is running. */
    private function viteDevServer(): array
    {
        $hot = public_path('hot');
        if (!File::exists($hot)) {
            return ['http' => '', 'ws' => ''];
        }

        $url = rtrim(trim(File::get($hot)), '/');

        return ['http' => $url, 'ws' => preg_replace('#^http#', 'ws', $url)];
    }
}
