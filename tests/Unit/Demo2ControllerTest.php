<?php

namespace Tests\Unit;

use App\Http\Controllers\HomeController;
use Tests\TestCase;

class Demo2ControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_demo2_method_returns_demo2_view(): void
    {
        $controller = new HomeController();

        $response = $controller->demo2();

        $this->assertSame('demo2', $response->name());
    }
}
