<?php

namespace TromsFylkestrafikk\RagnarokSTUB\Tests\Feature;

use TromsFylkestrafikk\RagnarokSTUB\Tests\TestCase;

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
