<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Demo3PageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_demo3_page_loads_successfully(): void
    {
        $response = $this->get('/demo3');

        $response->assertStatus(200)
            ->assertSee('id="demo3-hero-title"', false)
            ->assertSee('videos/bg.mp4')
            ->assertSee('videos/captions.vtt');
    }

    public function test_demo3_named_route_loads_successfully(): void
    {
        $response = $this->get(route('demo3'));

        $response->assertOk();
    }
}
