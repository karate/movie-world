@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-2 text-left">My Movies</h1>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <div class="flex flex-col gap-6">
        @forelse($movies as $movie)
            <div class="bg-white rounded shadow p-4 w-full">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold">{{ $movie->title }}</h2>
                    <div class="flex flex-col items-end text-sm text-gray-500">
                        <span>{{ $movie->created_at ? $movie->created_at->format('d/m/Y') : '' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-2 text-sm text-gray-500">
                    <span>{{ $movie->likes_count ?? ($movie->likes_count ?? 0) }} likes</span>
                    <span>|</span>
                    <span>{{ $movie->hates_count ?? ($movie->hates_count ?? 0) }} hates</span>
                </div>
                <form method="POST" action="{{ route('profile.movie.update', $movie) }}">
                    @csrf
                    @method('PATCH')
                    <textarea name="description" class="w-full border border-gray-300 rounded px-3 py-2 mb-2" rows="3">{{ old('description', $movie->description) }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">Update Description</button>
                    </div>
                </form>
            </div>
        @empty
            <p>You have not added any movies yet.</p>
        @endforelse
    </div>
</div>
@endsection
