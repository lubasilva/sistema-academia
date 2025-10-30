<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    protected $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['user', 'plan']);

        if (auth()->user()->role === 'student') {
            $query->where('user_id', auth()->id());
        }

        $payments = $query->latest()->paginate(20);

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('payments.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:boleto,pix,credit_card',
            'phone' => 'required|string|min:10',
            'cpf' => 'nullable|string|size:14',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $user = auth()->user();

        // Atualizar telefone e CPF do usuário se fornecidos
        $user->update([
            'phone' => preg_replace('/[^0-9]/', '', $validated['phone']),
            'cpf' => $validated['cpf'] ? preg_replace('/[^0-9]/', '', $validated['cpf']) : $user->cpf,
        ]);

        try {
            if ($validated['payment_method'] === 'pix') {
                $result = $this->asaasService->createPixPayment($user, $plan);
                
                $payment = Payment::where('asaas_payment_id', $result['id'])->first();
                
                if (!$payment) {
                    throw new \Exception('Falha ao criar registro de pagamento no banco de dados');
                }
                
                return redirect()->route('payments.show', $payment->id)
                    ->with('success', 'Pagamento PIX criado com sucesso!');
            } elseif ($validated['payment_method'] === 'boleto') {
                $result = $this->asaasService->createPayment($user, $plan);
                
                return redirect()->route('payments.index')
                    ->with('success', 'Cobrança criada com sucesso! Você receberá o boleto por e-mail.');
            } else {
                // credit_card
                return back()->with('error', 'Pagamento com cartão de crédito ainda não implementado.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Erro ao criar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return view('payments.show', compact('payment'));
    }

    public function adminIndex()
    {
        $payments = Payment::with(['user', 'plan'])->latest()->paginate(20);
        return view('payments.admin-index', compact('payments'));
    }
}

