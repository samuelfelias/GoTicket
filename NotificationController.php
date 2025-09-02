<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications.index')
                ->with('error', 'Você não tem permissão para visualizar esta notificação.');
        }
        
        // Marcar como lida se ainda não foi lida
        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->read_at = now();
            $notification->save();
        }
        
        return view('notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }
        
        $notification->is_read = true;
        $notification->read_at = now();
        $notification->save();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            
        return redirect()->route('notifications.index')
            ->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications.index')
                ->with('error', 'Você não tem permissão para excluir esta notificação.');
        }
        
        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notificação excluída com sucesso.');
    }
}
