# 🔗 Configuração de Webhooks Asaas - Ambiente Local

## 📋 URLs Configuradas

- **Rota do Webhook**: `POST /api/webhooks/asaas`
- **Controller**: `AsaasWebhookController@handle`

## 🚀 Opção 1: Ngrok (Recomendado)

### 1. Iniciar o Ngrok

Em um terminal separado, execute:

```bash
ngrok http 80
```

### 2. Copiar a URL gerada

Você verá algo como:
```
Forwarding  https://abc123.ngrok.io -> http://localhost:80
```

### 3. Configurar no Asaas

Acesse o painel do Asaas e configure o webhook com a URL:

```
https://abc123.ngrok.io/api/webhooks/asaas
```

**Eventos para ativar:**
- ✅ PAYMENT_RECEIVED
- ✅ PAYMENT_CONFIRMED
- ✅ PAYMENT_OVERDUE
- ✅ PAYMENT_DELETED
- ✅ PAYMENT_REFUNDED

### 4. Configurar o Webhook Secret (Opcional mas recomendado)

No `.env`, configure:

```env
ASAAS_WEBHOOK_SECRET=seu_token_secreto_aqui
```

No painel do Asaas, ao configurar o webhook, adicione um header customizado:
- **Header**: `asaas-access-token`
- **Valor**: `seu_token_secreto_aqui`

### 5. Testar o Webhook

```bash
# Ver logs em tempo real
./vendor/bin/sail artisan tail
```

Ou monitore o arquivo de log:
```bash
tail -f storage/logs/laravel.log
```

## 🔄 Opção 2: Expose (Alternativa ao Ngrok)

```bash
# Instalar expose
composer global require beyondcode/expose

# Iniciar expose
expose share http://localhost:80
```

Use a URL gerada: `https://xxx.sharedwithexpose.com/api/webhooks/asaas`

## 🧪 Opção 3: Teste Manual (sem ngrok)

Para testar localmente sem ngrok, use curl:

```bash
curl -X POST http://localhost/api/webhooks/asaas \
  -H "Content-Type: application/json" \
  -H "asaas-access-token: seu_token_secreto" \
  -d '{
    "event": "PAYMENT_CONFIRMED",
    "payment": {
      "id": "pay_123456789",
      "customer": "cus_123456789",
      "billingType": "PIX",
      "value": 100.00,
      "netValue": 98.50,
      "status": "RECEIVED",
      "description": "Plano Mensal",
      "dueDate": "2025-10-30",
      "confirmedDate": "2025-10-30"
    }
  }'
```

## 📊 Monitorar Webhooks

### Ver logs do webhook:

```bash
./vendor/bin/sail artisan tinker
```

```php
// Ver últimos logs
\Illuminate\Support\Facades\Log::info('Test');

// Ver pagamentos
\App\Models\Payment::latest()->get();

// Ver eventos processados
\App\Models\AuditLog::where('action', 'like', 'payment_%')->latest()->get();
```

## 🐛 Debug de Webhooks

### 1. Verificar se a rota está registrada:

```bash
./vendor/bin/sail artisan route:list | grep webhook
```

### 2. Verificar logs:

```bash
tail -f storage/logs/laravel.log | grep -i webhook
```

### 3. Testar manualmente com Postman/Insomnia:

**URL**: `http://localhost/api/webhooks/asaas`
**Method**: POST
**Headers**:
```json
{
  "Content-Type": "application/json",
  "asaas-access-token": "seu_token_secreto"
}
```

**Body (exemplo)**:
```json
{
  "event": "PAYMENT_CONFIRMED",
  "payment": {
    "id": "pay_test123",
    "value": 149.90,
    "status": "RECEIVED"
  }
}
```

## 🔐 Segurança

1. **Sempre use HTTPS** em produção
2. **Configure o webhook_secret** no `.env`
3. **Valide o IP de origem** (opcional):

```php
// No AsaasWebhookController
protected $asaasIps = [
    '164.152.0.0/16',
    // Adicionar IPs oficiais do Asaas
];
```

## 📱 Ngrok Pro (Opcional)

Se você tem conta Pro no ngrok, pode fixar um domínio:

```bash
ngrok http 80 --domain=seu-dominio.ngrok.io
```

Isso evita ter que reconfigurar o webhook toda vez que reiniciar o ngrok.

## ✅ Checklist

- [ ] Ngrok rodando
- [ ] Sail/servidor rodando
- [ ] URL do webhook configurada no Asaas
- [ ] Webhook secret configurado no `.env`
- [ ] Eventos marcados no painel Asaas
- [ ] Logs monitorados
- [ ] Teste manual executado

## 📞 Suporte

Se tiver problemas:
1. Verifique os logs: `storage/logs/laravel.log`
2. Teste com curl primeiro
3. Verifique se o webhook_token está correto
4. Confirme que a rota está acessível
