<?php

namespace TromsFylkestrafikk\RagnarokConsat\Tests\Feature;

use TromsFylkestrafikk\RagnarokConsat\Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
