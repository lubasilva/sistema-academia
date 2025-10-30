<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TestAsaasConnection extends Command
{
    protected $signature = 'asaas:test';
    protected $description = 'Testar conexão com API do Asaas';

    public function handle()
    {
        $apiKey = config('services.asaas.api_key');
        $baseUrl = config('services.asaas.base_url');

        $this->info("🔑 API Key: " . substr($apiKey, 0, 20) . '...');
        $this->info("🌐 Base URL: {$baseUrl}");
        $this->newLine();

        try {
            $client = new Client([
                'base_uri' => $baseUrl,
                'headers' => [
                    'access_token' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 10,
            ]);

            $this->info('📡 Testando conexão com Asaas...');
            
            // Testar listando clientes
            $response = $client->get('/customers', [
                'query' => ['limit' => 1]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            $this->info('✅ Conexão estabelecida com sucesso!');
            $this->info('📊 Total de clientes: ' . ($result['totalCount'] ?? 0));
            
            if (isset($result['data'][0])) {
                $this->newLine();
                $this->info('👤 Primeiro cliente encontrado:');
                $this->line('   ID: ' . $result['data'][0]['id']);
                $this->line('   Nome: ' . $result['data'][0]['name']);
                $this->line('   Email: ' . $result['data'][0]['email']);
            }

            return 0;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getBody()->getContents();
            $error = json_decode($responseBody, true);

            $this->error('❌ Erro HTTP ' . $statusCode);
            $this->error('Resposta: ' . $responseBody);
            
            if (isset($error['errors'])) {
                foreach ($error['errors'] as $err) {
                    $this->error('   - ' . $err['description']);
                }
            }

            return 1;
        } catch (\Exception $e) {
            $this->error('❌ Erro: ' . $e->getMessage());
            return 1;
        }
    }
}
