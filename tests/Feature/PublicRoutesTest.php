<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    /** @test */
    public function it_loads_the_home_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('home'); // Cambia 'home' por algo que esté en tu vista.
    }

    /** @test */
    public function it_loads_the_servicios_page()
    {
        $response = $this->get('/servicios');
        $response->assertStatus(200);
        $response->assertSee('servicios'); // Cambia 'servicios' por algo que esté en tu vista.
    }

    /** @test */
    public function it_redirects_to_login_when_accessing_protected_routes()
    {
        $response = $this->get('/appointments');
        $response->assertRedirect('/login');
    }
}
