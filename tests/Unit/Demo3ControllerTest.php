<?php

namespace Tests\Unit;

use App\Http\Controllers\HomeController;
use Tests\TestCase;

class Demo3ControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_demo3_method_returns_demo3_view(): void
    {
        $controller = new HomeController();

        $response = $controller->demo3();

        $this->assertSame('demo3', $response->name());
    }
}
