<?php 
namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Dto\MovieDto;

class MovieService
{
    public function createMovie(MovieDto $dto): Movie
    {
    $movie = Movie::create([
        'name' => $dto->name,
        'user_id' => $dto->userId,
    ]);

    $movie->categories()->attach($dto->categoryIds);

    return $movie;
    }
    

    public function updateMovie(Movie $movie, MovieDto $dto): bool
    {
        $categories = $dto->categoryIds ?? [];
        unset($dto->categoryIds);

    
        $updated = $movie->update([
            'name' => $dto->name
        ]);
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
