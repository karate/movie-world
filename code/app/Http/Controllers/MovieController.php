<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Movie::withCount([
            'reactions as likes_count' => fn($q) => $q->where('type', 'like'),
            'reactions as hates_count' => fn($q) => $q->where('type', 'hate'),
        ]);

        $username = null;
        if ($request->filled('user')) {
            $query->where('user_id', $request->input('user'));
            $userModel = \App\Models\User::find($request->input('user'));
            $username = $userModel ? $userModel->name : null;
        }

        // Sorting logic
        $sort = $request->input('sort', 'likes');
        if ($sort === 'likes') {
            $query->orderByDesc('likes_count');
        } elseif ($sort === 'hates') {
            $query->orderByDesc('hates_count');
        } elseif ($sort === 'date') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('likes_count'); // default fallback
        }

        $movies = $query->paginate(6)->appends($request->except('page'));

        // Attach the current user's reaction to each movie
        if (auth()->check()) {
            $userId = auth()->id();
            $movieIds = $movies->pluck('id');
            $reactions = \App\Models\Reaction::whereIn('movie_id', $movieIds)
                ->where('user_id', $userId)
                ->get()
                ->keyBy('movie_id');
            foreach ($movies as $movie) {
                $movie->user_reaction = $reactions[$movie->id]->type ?? null;
            }
        }

        return view('home', compact('movies', 'username'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('movies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $movie = new Movie();
        $movie->title = $validated['title'];
        $movie->description = $validated['description'] ?? null;
        $movie->user_id = $request->user()->id;
        $movie->save();

        return redirect()->route('home')->with('success', 'Movie added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
