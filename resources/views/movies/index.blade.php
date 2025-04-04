<x-app-layout>
    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Список фильмов</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Название</th>
                    <th class="px-4 py-2">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movies as $movie)
                    <tr>
                        <td class="border px-4 py-2">{{ $movie->name }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('movies.edit', $movie) }}" class="btn btn-sm btn-primary">Редактировать</a>
                            <form action="{{ route('movies.destroy', $movie) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('movies.create') }}" class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded">
            Добавить фильм
        </a>
    </div>
</x-app-layout>
