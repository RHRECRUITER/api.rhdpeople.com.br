<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Response;
use Tests\TestCase;

class ApiResponseMacroTest extends TestCase
{
    public function test_response_api_macro_formats_data_correctly()
    {
        // 1. Arrange & Act: Chamamos a macro diretamente sem passar por rotas
        $response = Response::api(['user' => 'Luan'], 'Criado com sucesso', 201);

        // Transformamos o JSON retornado em um array PHP para facilitar a leitura
        $data = $response->getData(true);

        // 2. Assert: Validamos se a estrutura segue o padrão do projeto
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($data['success']);
        $this->assertEquals('Criado com sucesso', $data['message']);
        $this->assertEquals(['user' => 'Luan'], $data['data']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('timestamp', $data['meta']);
    }

    public function test_health_endpoint_uses_the_api_macro()
    {
        // 1. Act: Fazemos um GET real na rota de healthcheck
        $response = $this->getJson('/api/v1/landing/health');

        // 2. Assert: Garantimos que o endpoint retornou a estrutura montada pela macro
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OK', // 'OK' é a mensagem padrão definida no seu README
                'data' => ['status' => 'ok'],
            ])
            ->assertJsonStructure(['meta' => ['timestamp']]);
    }
}
