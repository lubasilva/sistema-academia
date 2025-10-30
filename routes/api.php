<?php

use App\Http\Controllers\AsaasWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook Asaas (sem autenticação)
Route::post('/webhooks/asaas', [AsaasWebhookController::class, 'handle'])->name('webhooks.asaas');
