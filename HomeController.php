<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Removendo middleware de autenticação para a página inicial
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Buscar eventos em destaque
        $events = \App\Models\Event::orderBy('created_at', 'desc')->take(6)->get();
        
        return view('home', compact('events'));
    }
}
