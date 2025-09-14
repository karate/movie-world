<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Movie;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProfileController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show all movies belonging to the authenticated user.
     */
    public function movies(Request $request): View
    {
        $movies = $request->user()->movies()
            ->withCount([
                'reactions as likes_count' => function ($q) { $q->where('type', 'like'); },
                'reactions as hates_count' => function ($q) { $q->where('type', 'hate'); },
            ])
            ->latest()
            ->get();
        return view('profile.movies', compact('movies'));
    }

    /**
     * Update the description of a user's movie.
     */
    public function updateMovie(Request $request, Movie $movie): RedirectResponse
    {
        $this->authorize('update', $movie);
        $validated = $request->validate([
            'description' => 'nullable|string',
        ]);
        $movie->description = $validated['description'];
        $movie->save();
        return redirect()->route('profile.movies')->with('success', 'Movie description updated!');
    }
}
