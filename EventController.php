<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Venue;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }

    public function index()
    {
        $events = $this->eventRepository->getUpcomingEvents(12);
        return view('events.index', compact('events'));
    }

    public function show($id)
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            return redirect()->route('events.index')->with('error', 'Evento não encontrado.');
        }
        return view('events.show', compact('event'));
    }

    public function create()
    {
        $categories = Category::all();
        $venues = Venue::all();
        return view('events.create', compact('categories', 'venues'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_id' => 'required|exists:venues,id',
            'category_id' => 'required|exists:categories,id',
            'available_tickets' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        $event = $this->eventRepository->create($data);

        return redirect()->route('events.show', $event->id)->with('success', 'Evento criado com sucesso!');
    }

    public function edit($id)
    {
        $event = $this->eventRepository->find($id);
        if (!$event || $event->user_id !== Auth::id()) {
            return redirect()->route('events.index')->with('error', 'Você não tem permissão para editar este evento.');
        }

        $categories = Category::all();
        $venues = Venue::all();
        return view('events.edit', compact('event', 'categories', 'venues'));
    }

    public function update(Request $request, $id)
    {
        $event = $this->eventRepository->find($id);
        if (!$event || $event->user_id !== Auth::id()) {
            return redirect()->route('events.index')->with('error', 'Você não tem permissão para editar este evento.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_id' => 'required|exists:venues,id',
            'category_id' => 'required|exists:categories,id',
            'available_tickets' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        $this->eventRepository->update($id, $data);

        return redirect()->route('events.show', $id)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $event = $this->eventRepository->find($id);
        if (!$event || $event->user_id !== Auth::id()) {
            return redirect()->route('events.index')->with('error', 'Você não tem permissão para excluir este evento.');
        }

        $this->eventRepository->delete($id);

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');
        $date = $request->input('date');
        $venue = $request->input('venue');

        $events = $this->eventRepository->searchEvents($query, $category, $date, $venue);
        $categories = Category::all();
        $venues = Venue::all();

        return view('events.search', compact('events', 'categories', 'venues', 'query', 'category', 'date', 'venue'));
    }
}
