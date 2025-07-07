<?php 
namespace App\Services;

use App\Models\UserAction;
use Illuminate\Support\Facades\Auth;

class UserActionLogger
{
    public function log(string $actionType, ?int $movieId = null, array $metadata = []): void
    {
        UserAction::create([
            'user_id' => Auth::id(),
            'action_type' => $actionType,
            'movie_id' => $movieId,
            'metadata' => $metadata,
        ]);
    }

    // Удобные шорткаты
    public function logView(int $movieId): void
    {
        $this->log('view', $movieId);
    }

    public function logCreate(int $movieId): void
    {
        $this->log('create', $movieId);
    }

    public function logUpdate(int $movieId): void
    {
        $this->log('update', $movieId);
    }

    public function logDelete(int $movieId): void
    {
        $this->log('delete', $movieId);
    }
}
