<?php

namespace App\Mappers;

use App\Dto\MovieDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieMapper
{
    public static function fromRequest(Request $request): MovieDto
    {
        return new MovieDto(
            name: $request->input('name'),
            userId: Auth::id(),
            categoryIds: $request->input('categories', [])
        );
    }
}
