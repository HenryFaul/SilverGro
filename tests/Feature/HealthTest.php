<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok()
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status','app','env']);
    }
}

