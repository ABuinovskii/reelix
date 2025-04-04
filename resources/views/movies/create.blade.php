<x-app-layout>
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Добавить фильм</h2>
        <form action="{{ route('movies.store') }}" method="POST">
            @csrf

            <!-- Название фильма -->
            <div class="mb-3">
                <label for="name" class="block font-medium text-gray-700">Название</label>
                <input type="text" name="name" id="name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    required>
            </div>
            


            <div class="mb-3">
    <label class="block font-medium text-gray-700">Категории</label>
    @foreach ($categories as $category)
        <div>
            <label>
                <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                {{ $category->name }}
            </label>
        </div>
    @endforeach
</div>





            <!-- Кнопка отправки -->
            <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Добавить
            </button>
        </form>
    </div>
</x-app-layout>
