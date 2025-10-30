<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.asaas.api_key');
        $this->baseUrl = config('services.asaas.base_url');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'StudioFit/1.0',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Criar ou atualizar cliente no Asaas
     */
    public function createOrUpdateCustomer($user)
    {
        try {
            // Validar dados obrigatórios
            if (empty($user->phone)) {
                throw new \Exception('Telefone é obrigatório para criar cobrança. Por favor, atualize seu perfil.');
            }

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => preg_replace('/[^0-9]/', '', $user->phone), // Remove formatação
                'cpfCnpj' => $user->cpf ? preg_replace('/[^0-9]/', '', $user->cpf) : null,
                'externalReference' => (string) $user->id,
            ];

            if ($user->asaas_customer_id) {
                // Atualizar cliente existente
                $response = $this->client->put("/customers/{$user->asaas_customer_id}", [
                    'json' => $data
                ]);
            } else {
                // Criar novo cliente
                $response = $this->client->post('/customers', [
                    'json' => $data
                ]);
            }

            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);
            
            // Log da resposta para debug
            Log::info('Resposta do Asaas ao criar cliente', [
                'user_id' => $user->id,
                'response_body' => $responseBody,
                'decoded' => $result,
                'has_id' => isset($result['id']),
            ]);
            
            // Verificar se houve erro na resposta
            if (!$result || !isset($result['id'])) {
                Log::error('Resposta inválida do Asaas ao criar cliente', [
                    'user_id' => $user->id,
                    'response' => $responseBody,
                    'result' => $result
                ]);
                
                $errorMsg = 'Erro desconhecido';
                if (isset($result['errors']) && is_array($result['errors']) && count($result['errors']) > 0) {
                    $errorMsg = $result['errors'][0]['description'] ?? $errorMsg;
                }
                
                throw new \Exception('Resposta inválida da API Asaas: ' . $errorMsg);
            }
            
            if (!$user->asaas_customer_id) {
                $user->update(['asaas_customer_id' => $result['id']]);
            }

            return $result;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $error = json_decode($responseBody, true);
            
            Log::error('Erro HTTP ao criar/atualizar cliente Asaas', [
                'user_id' => $user->id,
                'status' => $e->getResponse()->getStatusCode(),
                'response' => $responseBody,
                'error' => $error
            ]);
            
            $errorMsg = $error['errors'][0]['description'] ?? $e->getMessage();
            throw new \Exception("Erro ao criar cliente no Asaas: {$errorMsg}");
        } catch (\Exception $e) {
            Log::error('Erro ao criar/atualizar cliente Asaas', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Criar cobrança
     */
    public function createPayment($user, $plan, $dueDate = null)
    {
        try {
            if (!$user->asaas_customer_id) {
                $this->createOrUpdateCustomer($user);
            }

            $data = [
                'customer' => $user->asaas_customer_id,
                'billingType' => 'BOLETO', // PIX, CREDIT_CARD, UNDEFINED
                'value' => $plan->price,
                'dueDate' => $dueDate ?? now()->addDays(3)->format('Y-m-d'),
                'description' => "Plano {$plan->name} - StudioFit",
                'externalReference' => "plan_{$plan->id}_user_{$user->id}",
                'installmentCount' => 1,
                'installmentValue' => $plan->price,
            ];

            $response = $this->client->post('/payments', [
                'json' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Salvar pagamento no banco
            \App\Models\Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'pending',
                'asaas_payment_id' => $result['id'],
                'due_date' => $data['dueDate'],
                'payment_method' => 'boleto',
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Erro ao criar cobrança Asaas', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Consultar pagamento
     */
    public function getPayment($paymentId)
    {
        try {
            $response = $this->client->get("/payments/{$paymentId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar pagamento Asaas', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Gerar PIX
     */
    public function createPixPayment($user, $plan, $expirationMinutes = 30)
    {
        try {
            if (!$user->asaas_customer_id) {
                $this->createOrUpdateCustomer($user);
            }

            $data = [
                'customer' => $user->asaas_customer_id,
                'billingType' => 'PIX',
                'value' => $plan->price,
                'dueDate' => now()->format('Y-m-d'),
                'description' => "Plano {$plan->name} - StudioFit",
                'externalReference' => "plan_{$plan->id}_user_{$user->id}_pix",
            ];

            $response = $this->client->post('/payments', [
                'json' => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Obter QR Code do PIX
            $pixResponse = $this->client->get("/payments/{$result['id']}/pixQrCode");
            $pixData = json_decode($pixResponse->getBody()->getContents(), true);

            // Salvar pagamento no banco
            \App\Models\Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'pending',
                'asaas_payment_id' => $result['id'],
                'due_date' => $data['dueDate'],
                'payment_method' => 'pix',
                'pix_qr_code' => $pixData['encodedImage'] ?? null,
                'pix_payload' => $pixData['payload'] ?? null,
            ]);

            return array_merge($result, ['pix' => $pixData]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar PIX Asaas', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancelar cobrança
     */
    public function cancelPayment($paymentId)
    {
        try {
            $response = $this->client->delete("/payments/{$paymentId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar pagamento Asaas', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Listar cobranças de um cliente
     */
    public function getCustomerPayments($customerId)
    {
        try {
            $response = $this->client->get("/payments", [
                'query' => [
                    'customer' => $customerId,
                    'limit' => 100
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Erro ao listar pagamentos do cliente Asaas', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
