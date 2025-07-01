<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Services\MovieService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Mappers\MovieMapper;
use App\Services\MovieViewCounterService;
use Illuminate\Support\Facades\Redis;

class MovieController extends Controller
{

    protected $movieService;
    private MovieViewCounterService $viewCounter;

    public function __construct(MovieService $movieService, MovieViewCounterService $viewCounter){
        $this->movieService = $movieService;
        $this->viewCounter = $viewCounter;
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['category_id']);
        $movies = $this->movieService->getFilteredMovies($filters);
        $categories = Category::all();
    
        return view('movies.index', compact('movies', 'categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create():View 
    {
        
        $categories = Category::all();
        return view('movies.create', compact('categories'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $dto = MovieMapper::fromRequest($request);

        $this->movieService->createMovie($dto);

        return redirect()->route('movies.index')->with('success', 'Added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
{
   
    $this->viewCounter->increment($movie->id);
    $baseViews = $movie->views;
    $redisViews = $this->viewCounter->get($movie->id);
    $totalViews = $baseViews + $redisViews;

    return view('movies.show', [
        'movie' => $movie,
        'totalViews' => $totalViews,
    ]);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        $categories = Category::all();
        return view('movies.edit', compact('movie', 'categories'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie): RedirectResponse 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $dto = MovieMapper::fromRequest($request);

        $this->movieService->updateMovie($movie, $dto);
        return redirect()->route('movies.index')->with('success', 'Updated!');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie): RedirectResponse
    {
    
        $this->movieService->deleteMovie($movie);

        return redirect()->route('movies.index')->with('success', 'Фильм успешно удалён!');
    }

    public function topViewed()
{
    $movies = Movie::with('categories')->get();

    $moviesWithViews = $movies->map(function ($movie) {
        $baseViews = $movie->views;
        $redisViews = (int) Redis::get("movie:{$movie->id}:views");
        $movie->total_views = $baseViews + $redisViews;
        return $movie;
    });

    $topViewedMovies = $moviesWithViews
        ->sortByDesc('total_views')
        ->take(5);

    return view('movies.top_viewed', [
        'topViewedMovies' => $topViewedMovies,
    ]);
}

}
