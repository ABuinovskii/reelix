<x-app-layout>
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Редактировать фильм</h2>

        <form action="{{ route('movies.update', $movie) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="block font-medium text-gray-700">Название</label>
                <input type="text" name="name" id="name" value="{{ $movie->name }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    required>
            </div>



            @foreach ($categories as $category)
    <div>
        <label>
            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                {{ $movie->categories->contains($category->id) ? 'checked' : '' }}>
            {{ $category->name }}
        </label>
    </div>
@endforeach





            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Сохранить
            </button>
        </form>
    </div>
</x-app-layout>
