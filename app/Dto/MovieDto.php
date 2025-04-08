<?php

namespace App\Dto;

class MovieDto
{
    public string $name;
    public int $userId;
    public array $categoryIds;

    public function __construct(string $name, int $userId, array $categoryIds = [])
    {
        $this->name = $name;
        $this->userId = $userId;
        $this->categoryIds = $categoryIds;
    }
}
