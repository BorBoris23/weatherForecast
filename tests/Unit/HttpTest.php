<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    public function test_a_basic_request(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
