<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_redirects_to_login(): void
    {
        // El registro público está deshabilitado; /register redirige a login
        $this->get('/register')->assertRedirect(route('login'));
    }

    public function test_registration_post_redirects_to_login(): void
    {
        $this->post('/register', [])->assertRedirect(route('login'));
    }
}
