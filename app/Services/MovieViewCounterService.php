<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class MovieViewCounterService
{
    public function increment(int $movieId): void
    {
        Redis::incr($this->key($movieId));
    }

    public function get(int $movieId): int
    {
        return (int) Redis::get($this->key($movieId)) ?? 0;
    }

    public function pullAll(): array
{
    $keys = Redis::keys('movie:*:views'); // Laravel возвращает с префиксом

    if (empty($keys)) {
        return [];
    }

    $result = [];

    foreach ($keys as $key) {
      
        preg_match('/movie:(\d+):views/', $key, $matches);
        $movieId = isset($matches[1]) ? (int) $matches[1] : null;

        if ($movieId !== null) {
            
            $logicalKey = "movie:{$movieId}:views";

       
            $views = Redis::get($logicalKey);

            $result[$movieId] = (int) $views;
        }
    }

    return $result;
}





    


    public function forget(int $movieId): void
    {
        Redis::del($this->key($movieId));
    }

    public function forgetAll(): void
    {
        $keys = Redis::keys('movie:*:views');
        foreach ($keys as $key) {
            Redis::del($key);
        }
    }

    private function key(int $movieId): string
    {
        return "movie:{$movieId}:views";
    }
    



}
