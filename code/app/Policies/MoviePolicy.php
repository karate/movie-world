<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    /**
     * Determine if the given movie can be updated by the user.
     */
    public function update(User $user, Movie $movie): bool
    {
        return $user->id === $movie->user_id;
    }
}
