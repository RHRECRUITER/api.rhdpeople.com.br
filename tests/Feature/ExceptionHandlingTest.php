<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    public function test_it_returns_standardized_404_for_unknown_api_routes()
    {
        $response = $this->getJson('/api/v1/landing/rota-inexistente');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Not found',
                'data' => null,
            ])
            ->assertJsonStructure(['meta' => ['timestamp']]);
    }

    public function test_it_returns_standardized_405_for_wrong_http_method()
    {
        // A rota /api/v1/landing/leads existe, mas é POST. Fazer um GET deve retornar 405.
        $response = $this->getJson('/api/v1/landing/leads');

        $response->assertStatus(405)
            ->assertJson([
                'success' => false,
                'message' => 'Method not allowed',
                'data' => null,
            ]);
    }
}
