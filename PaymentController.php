<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->middleware('auth');
    }

    public function index()
    {
        $payments = $this->paymentRepository->getUserPayments(Auth::id());
        return view('payments.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = $this->paymentRepository->find($id);
        
        if (!$payment || $payment->user_id !== Auth::id()) {
            return redirect()->route('payments.index')->with('error', 'Pagamento não encontrado ou você não tem permissão para visualizá-lo.');
        }
        
        return view('payments.show', compact('payment'));
    }

    public function callback(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');
        
        $payment = $this->paymentRepository->findByTransactionId($transactionId);
        
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pagamento não encontrado.'], 404);
        }
        
        $this->paymentRepository->updatePaymentStatus($payment->id, $status);
        
        return response()->json(['success' => true]);
    }

    public function receipt($id)
    {
        $payment = $this->paymentRepository->find($id);
        
        if (!$payment || $payment->user_id !== Auth::id()) {
            return redirect()->route('payments.index')->with('error', 'Recibo não encontrado ou você não tem permissão para visualizá-lo.');
        }
        
        return view('payments.receipt', compact('payment'));
    }
}
