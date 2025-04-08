<x-app-layout>
<form method="GET" action="{{ route('movies.index') }}" class="mb-4">
    <label for="category_id" class="block mb-1 font-medium">Filter by genre:</label>
    <select name="category_id" id="category_id" class="rounded border px-3 py-2">
        <option value="">All genres</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
        Filter
    </button>
</form>

    <div class="container mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">List</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movies as $movie)
                    <tr>
                        <td class="border px-4 py-2">{{ $movie->name }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('movies.edit', $movie) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('movies.destroy', $movie) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('movies.create') }}" class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded">
            Add movie
        </a>
    </div>
</x-app-layout>
