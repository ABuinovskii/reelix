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


class MovieController extends Controller
{

    protected $movieService;

    public function __construct(MovieService $movieService){
        $this->movieService = $movieService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $movies = $this->movieService->getAllMovies();
        return view('movies.index', compact('movies'));
        
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

        // Выносим логику в сервис
        $this->movieService->createMovie($validated);

        // 4️⃣ Выводим уведомление и перенаправляем обратно
        return redirect()->route('movies.index')->with('success', 'Фильм успешно добавлен!');
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
        $categories = Category::all();
        return view('movies.edit', compact('movie', 'categories'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie): RedirectResponse 
    {
            // Валидация данных
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id',
            ]);

        // Вызываем сервис
        $this->movieService->updateMovie($movie, $validated);

        return redirect()->route('movies.index')->with('success', 'Фильм успешно обновлён!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie): RedirectResponse
    {
        // Вызываем сервис
        $this->movieService->deleteMovie($movie);

        return redirect()->route('movies.index')->with('success', 'Фильм успешно удалён!');
    }
}
