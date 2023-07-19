<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_security_headers_are_sent(): void
    {
        $this->withoutVite();
        $response = $this->get('/auth/login');

        $response->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[A-Za-z0-9]+'/", $csp);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp);
    }

    public function test_each_response_gets_a_fresh_nonce(): void
    {
        $this->withoutVite();
        $first = $this->get('/auth/login')->headers->get('Content-Security-Policy');
        $second = $this->get('/auth/login')->headers->get('Content-Security-Policy');

        preg_match("/'nonce-([^']+)'/", $first, $a);
        preg_match("/'nonce-([^']+)'/", $second, $b);
        $this->assertNotSame($a[1], $b[1]);
    }

    public function test_session_cookie_is_strict_and_http_only(): void
    {
        $cookie = collect($this->get('/auth/login')->headers->getCookies())
            ->first(fn ($c) => $c->getName() === config('session.cookie'));

        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('strict', $cookie->getSameSite());
    }
}
