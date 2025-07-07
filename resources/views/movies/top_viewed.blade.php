<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Top 5 Most Viewed Movies
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto space-y-4">
        @foreach ($topViewedMovies as $movie)
            <div class="p-4 bg-white shadow rounded">
                <h3 class="text-lg font-bold">{{ $movie->name }}</h3>
                <p>Views: {{ $movie->total_views }}</p>
                <p>Categories:
                    @foreach ($movie->categories as $category)
                        <span class="inline-block bg-gray-200 text-sm px-2 py-1 rounded">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </p>
            </div>
        @endforeach
    </div>
</x-app-layout>
