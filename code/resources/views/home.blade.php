@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8" >
    <h1 class="text-3xl font-bold mb-2 text-left">
        @if(isset($username) && $username)
            All movies by {{ $username }}
        @else
            All Movies
        @endif
    </h1>
    <div class="mb-6 text-left">Found {{ $movies->total() }} movies.</div>
    <div class="flex flex-col-reverse md:flex-row gap-8">
        <!-- Left Column: Movies list -->
        <div class="flex-1 flex flex-col gap-6">
            @forelse($movies as $movie)
                <div class="bg-white rounded shadow p-4 w-full">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-semibold">{{ $movie->title }}</h2>
                        <div class="flex flex-col items-end text-sm text-gray-500">
                            <span>{{ $movie->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <p class="mb-2 text-gray-700">{{ $movie->description }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <span class="text-green-800" id="likes-count-{{ $movie->id }}">{{ $movie->likes_count ?? 0 }} likes</span>
                            <span>|</span>
                            <span class="text-red-600" id="hates-count-{{ $movie->id }}">{{ $movie->hates_count ?? 0 }} hates</span>
                        </div>
                        @auth
                        @if($movie->user_id !== auth()->id())
                        <div class="flex items-center gap-2">
                            <a href="#"
                               class="like-btn {{ $movie->user_reaction === 'like' ? 'bg-blue-100 text-blue-800' : 'text-blue-600 hover:bg-blue-50' }} px-2 py-1 rounded"
                               data-movie="{{ $movie->id }}" data-type="like">Like</a>
                            <span>|</span>
                            <a href="#"
                               class="hate-btn {{ $movie->user_reaction === 'hate' ? 'bg-red-100 text-red-800' : 'text-red-600 hover:bg-red-50' }} px-2 py-1 rounded"
                               data-movie="{{ $movie->id }}" data-type="hate">Hate</a>
                        </div>
                        @endif
                        @endauth
                        <div>by <a href="{{ route('home', ['user' => $movie->user->id]) }}" class="text-blue-600 hover:underline">{{ $movie->user->name }}</a></div>
                    </div>
                </div>
            @empty
                <p>No movies found.</p>
            @endforelse
        </div>
        <!-- Right Column: New Movie Button and Sort By -->
        <div class="w-full md:w-72 flex-shrink-0 flex flex-col items-center md:items-end mb-8 md:mb-0">
            @auth
                <a href="{{ route('movies.create') }}" class="bg-green-600 text-white w-full px-6 py-3 rounded hover:bg-green-700 text-lg font-semibold whitespace-nowrap mb-6 text-center block">New Movie</a>
            @endauth
            <form method="GET" action="" class="w-full bg-white rounded shadow p-4 flex flex-col gap-2" id="sortForm">
                <label class="font-semibold mb-2">Sort by:</label>
                <div class="flex flex-col gap-1">
                    <label class="inline-flex items-center">
                        <input type="radio" name="sort" value="likes" class="form-radio text-blue-600" {{ request('sort', 'likes') == 'likes' ? 'checked' : '' }} onchange="document.getElementById('sortForm').submit()">
                        <span class="ml-2">Likes</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="sort" value="hates" class="form-radio text-red-600" {{ request('sort') == 'hates' ? 'checked' : '' }} onchange="document.getElementById('sortForm').submit()">
                        <span class="ml-2">Hates</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="sort" value="date" class="form-radio text-gray-600" {{ request('sort') == 'date' ? 'checked' : '' }} onchange="document.getElementById('sortForm').submit()">
                        <span class="ml-2">Date added</span>
                    </label>
                </div>
            </form>
        </div>
    </div>
</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateReactionCount(movieId, type, count) {
        if (type === 'like') {
            document.getElementById('likes-count-' + movieId).textContent = count + ' likes';
        } else if (type === 'hate') {
            document.getElementById('hates-count-' + movieId).textContent = count + ' hates';
        }
    }

    function updateUserReactionHighlight(movieId, newType) {
        const likeBtn = document.querySelector('.like-btn[data-movie="' + movieId + '"]');
        const hateBtn = document.querySelector('.hate-btn[data-movie="' + movieId + '"]');
        if (likeBtn && hateBtn) {
            // Reset all
            likeBtn.classList.remove('bg-blue-100', 'text-blue-800', 'hover:bg-blue-50');
            likeBtn.classList.add('text-blue-600', 'hover:bg-blue-50');
            hateBtn.classList.remove('bg-red-100', 'text-red-800', 'hover:bg-red-50');
            hateBtn.classList.add('text-red-600', 'hover:bg-red-50');
            if (newType === 'like') {
                likeBtn.classList.add('bg-blue-100', 'text-blue-800');
                likeBtn.classList.remove('text-blue-600');
            } else if (newType === 'hate') {
                hateBtn.classList.add('bg-red-100', 'text-red-800');
                hateBtn.classList.remove('text-red-600');
            }
        }
    }

    document.querySelectorAll('.like-btn, .hate-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.getAttribute('data-movie');
            const type = this.getAttribute('data-type');
            fetch(`/movies/${movieId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ type })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateReactionCount(movieId, 'like', data.likes_count);
                    updateReactionCount(movieId, 'hate', data.hates_count);
                    // If user retracted their vote, data will not have a new type, so check which count changed
                    updateUserReactionHighlight(movieId, data.user_reaction);
                } else if (data.message) {
                    alert(data.message);
                }
            })
            .catch((e) => {
                console.log(e);
                alert('An error occurred. Please try again.');
            });
        });
    });
});
</script>
@endauth

@endsection
