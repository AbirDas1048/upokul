<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Demo1PageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_demo1_page_loads_successfully(): void
    {
        $response = $this->get('/demo1');

        $response->assertStatus(200);
    }

    public function test_demo1_named_route_loads_successfully(): void
    {
        $response = $this->get(route('demo1'));

        $response->assertOk();
    }
}
