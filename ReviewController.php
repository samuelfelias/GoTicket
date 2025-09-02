<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Verificar se o usuário já avaliou este evento
        $existingReview = Review::where('user_id', Auth::id())
            ->where('event_id', $eventId)
            ->first();
            
        if ($existingReview) {
            return redirect()->route('events.show', $eventId)->with('error', 'Você já avaliou este evento.');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);
        
        Review::create([
            'user_id' => Auth::id(),
            'event_id' => $eventId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-aprovação para simplificar
        ]);
        
        return redirect()->route('events.show', $eventId)->with('success', 'Avaliação enviada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('events.show', $review->event_id)->with('error', 'Você não tem permissão para editar esta avaliação.');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);
        
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Mantém aprovado ao editar
        ]);
        
        return redirect()->route('events.show', $review->event_id)->with('success', 'Avaliação atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $review->event_id)->with('error', 'Você não tem permissão para excluir esta avaliação.');
        }
        
        $eventId = $review->event_id;
        $review->delete();
        
        return redirect()->route('events.show', $eventId)->with('success', 'Avaliação excluída com sucesso!');
    }
}
