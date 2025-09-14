<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Reaction;

class ReactionController extends Controller
{
    public function store(Request $r, Movie $movie)
    {
        if ($movie->user_id === auth()->id()) {
            if ($r->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You cannot vote on your own movie.'], 403);
            }
            return back()->with('error','You cannot vote on your own movie.');
        }
        $r->validate(['type'=>'required|in:like,hate']);
        $reaction = Reaction::firstOrNew([
            'movie_id'=>$movie->id,
            'user_id'=>auth()->id(),
        ]);

        if($reaction->exists && $reaction->type === $r->type){
            // same vote → retract
            $reaction->delete();
        } else {
            $reaction->type = $r->type;
            $reaction->save();
        }

        $likes_count = $movie->reactions()->where('type', 'like')->count();
        $hates_count = $movie->reactions()->where('type', 'hate')->count();

        // Get the user's current reaction for this movie
        $user_reaction = null;
        $currentReaction = $movie->reactions()->where('user_id', auth()->id())->first();
        if ($currentReaction) {
            $user_reaction = $currentReaction->type;
        }

        if ($r->expectsJson()) {
            return response()->json([
                'success' => true,
                'likes_count' => $likes_count,
                'hates_count' => $hates_count,
                'user_reaction' => $user_reaction,
            ]);
        }
        return back();
    }
}
