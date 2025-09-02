<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $ticketRepository;
    protected $paymentRepository;

    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->paymentRepository = $paymentRepository;
        $this->middleware('auth');
    }

    public function index()
    {
        $tickets = $this->ticketRepository->getUserTickets(Auth::id());
        return view('tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = $this->ticketRepository->find($id);
        
        if (!$ticket || $ticket->user_id !== Auth::id()) {
            return redirect()->route('tickets.index')->with('error', 'Ingresso não encontrado ou você não tem permissão para visualizá-lo.');
        }
        
        return view('tickets.show', compact('ticket'));
    }

    public function purchase(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if ($event->available_tickets < 1) {
            return redirect()->route('events.show', $eventId)->with('error', 'Não há mais ingressos disponíveis para este evento.');
        }
        
        $request->validate([
            'payment_method' => 'required|string|in:credit_card,debit_card,pix,bank_transfer',
            'quantity' => 'required|integer|min:1|max:' . $event->available_tickets,
        ]);
        
        $quantity = $request->input('quantity', 1);
        $totalAmount = $event->price * $quantity;
        
        // Processar pagamento
        $paymentData = [
            'user_id' => Auth::id(),
            'event_id' => $eventId,
            'amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'payment_details' => json_encode([
                'event_name' => $event->name,
                'quantity' => $quantity,
                'unit_price' => $event->price,
            ]),
        ];
        
        $payment = $this->paymentRepository->processPayment($paymentData);
        
        if ($payment->status === 'completed') {
            // Criar ingressos
            for ($i = 0; $i < $quantity; $i++) {
                $this->ticketRepository->create([
                    'event_id' => $eventId,
                    'user_id' => Auth::id(),
                    'payment_id' => $payment->id,
                    'status' => 'active',
                ]);
            }
            
            // Atualizar quantidade de ingressos disponíveis
            $event->available_tickets -= $quantity;
            $event->save();
            
            return redirect()->route('tickets.index')->with('success', 'Ingressos comprados com sucesso!');
        }
        
        return redirect()->route('events.show', $eventId)->with('error', 'Falha no processamento do pagamento. Por favor, tente novamente.');
    }

    public function transfer(Request $request, $id)
    {
        $ticket = $this->ticketRepository->find($id);
        
        if (!$ticket || $ticket->user_id !== Auth::id()) {
            return redirect()->route('tickets.index')->with('error', 'Ingresso não encontrado ou você não tem permissão para transferi-lo.');
        }
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $result = $this->ticketRepository->transferTicket($id, $request->email);
        
        if ($result) {
            return redirect()->route('tickets.index')->with('success', 'Ingresso transferido com sucesso!');
        }
        
        return redirect()->route('tickets.show', $id)->with('error', 'Não foi possível transferir o ingresso. Por favor, tente novamente.');
    }

    public function validate($code)
    {
        $ticket = $this->ticketRepository->findByTicketCode($code);
        
        if (!$ticket) {
            return response()->json(['valid' => false, 'message' => 'Ingresso não encontrado.']);
        }
        
        if ($ticket->status !== 'active') {
            return response()->json(['valid' => false, 'message' => 'Ingresso inválido ou já utilizado.']);
        }
        
        if ($ticket->used_at) {
            return response()->json(['valid' => false, 'message' => 'Ingresso já foi utilizado em ' . $ticket->used_at->format('d/m/Y H:i:s')]);
        }
        
        // Marcar ingresso como utilizado
        $this->ticketRepository->update($ticket->id, [
            'status' => 'used',
            'used_at' => now(),
        ]);
        
        return response()->json([
            'valid' => true, 
            'message' => 'Ingresso válido!',
            'ticket' => [
                'event' => $ticket->event->name,
                'user' => $ticket->user->name,
                'date' => $ticket->event->start_date->format('d/m/Y H:i'),
                'venue' => $ticket->event->venue->name,
            ]
        ]);
    }
}
