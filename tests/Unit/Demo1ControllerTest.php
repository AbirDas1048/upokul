<?php

namespace Tests\Unit;

use App\Http\Controllers\HomeController;
use Tests\TestCase;

class Demo1ControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_demo1_method_returns_demo1_view(): void
    {
        $controller = new HomeController();

        $response = $controller->demo1();

        $this->assertSame('demo1', $response->name());
    }
}
