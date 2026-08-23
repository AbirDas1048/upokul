<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Demo2PageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_demo2_page_loads_successfully(): void
    {
        $response = $this->get('/demo2');

        $response->assertStatus(200)
            ->assertSee('id="demo2-hero-title"', false)
            ->assertSee('Travel agency highlights')
            ->assertSee('4.9★');
    }

    public function test_demo2_named_route_loads_successfully(): void
    {
        $response = $this->get(route('demo2'));

        $response->assertOk();
    }
}
