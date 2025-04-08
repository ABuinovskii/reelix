<?php 
namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MovieService
{
    public function createMovie(array $data): Movie
    {
        $data['user_id'] = Auth::id();

        // Извлекаем категории отдельно
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $movie = Movie::create($data);
        $movie->categories()->attach($categories);

        return $movie;

    }

    public function updateMovie(Movie $movie, array $data): bool
    {
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        $updated = $movie->update($data);
        $movie->categories()->sync($categories);

        return $updated;
    }

    public function deleteMovie(Movie $movie): bool
    {
        return $movie->delete();
    }

    public function getAllMovies() :Collection
    {
        return Movie::with('categories')->get();
    }


    public function getFilteredMovies(array $filters = []): Collection
    {
        $query = Movie::with('categories');

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        return $query->get();
    }

}
