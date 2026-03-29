<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertSuccessful();
    }

    public function test_forgot_password_page_loads(): void
    {
        $this->get('/forgot-password')->assertSuccessful();
    }

    public function test_reset_password_page_loads(): void
    {
        $this->get('/reset-password/test-token?email=test@example.com')->assertSuccessful();
    }

    public function test_login_with_invalid_credentials_returns_errors(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }
}
