<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class GuzzleIntegrationTest extends TestCase
{
    public function testApiReturns200Status()
    {
        $response = Http::get('http://127.0.0.1:8000/login');
        $this->assertEquals(200, $response->getStatusCode());
    }

}
