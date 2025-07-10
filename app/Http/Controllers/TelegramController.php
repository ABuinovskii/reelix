<?php
namespace App\Http\Controllers;

use App\Models\TelegramSession;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Api;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    protected Api $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('services.telegram-bot-api.token'));
    }

    public function webhook(Request $request)
    {
        $update = $this->telegram->getWebhookUpdate();
        $message = $update->getMessage();
        $chatId = $message->chat->id;
        $text = trim($message->text ?? '');

        // Проверяем авторизацию
        $session = TelegramSession::where('chat_id', $chatId)->first();
        if (!$session) {
            // Регистрация
            if (str_starts_with($text, '/register ')) {
                [$email, $password] = explode(' ', substr($text, 10), 2);
                $user = User::where('email', $email)->first();
                if ($user) {
                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'Пользователь с таким email уже существует. Используйте /login',
                    ]);
                    return;
                }

                $validator = Validator::make([
                    'email' => $email,
                    'password' => $password,
                ], [
                    'email' => 'required|email',
                    'password' => 'required|min:4',
                ]);

                if ($validator->fails()) {
                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'Неверный формат email или слишком короткий пароль.',
                    ]);
                    return;
                }

                $user = User::create([
                    'name' => $email,
                    'email' => $email,
                    'password' => bcrypt($password),
                ]);

                TelegramSession::create([
                    'chat_id' => $chatId,
                    'user_id' => $user->id,
                ]);

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Регистрация прошла успешно!',
                ]);
                return;
            }

            // Логин
            if (str_starts_with($text, '/login ')) {
                [$email, $password] = explode(' ', substr($text, 7), 2);
                $user = User::where('email', $email)->first();
                if (!$user || !Hash::check($password, $user->password)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'Неверный email или пароль.',
                    ]);
                    return;
                }

                TelegramSession::updateOrCreate(
                    ['chat_id' => $chatId],
                    ['user_id' => $user->id]
                );

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Вы успешно вошли!',
                ]);
                return;
            }

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Вы не авторизованы. Используйте /register или /login\nПример:\n/register email@example.com password\n/login email@example.com password",
            ]);
            return;
        }

        // Авторизованный пользователь
        $user = $session->user;

        if (str_starts_with($text, '/add ')) {
            $name = trim(substr($text, 5));
            if (!$name) {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Введите название фильма: /add Название',
                ]);
                return;
            }

            $dto = new \App\DTO\MovieDto(
                name: $name,
                userId: $user->id,
                categoryIds: []
            );

            app(\App\Services\MovieService::class)->createMovie($dto);

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Фильм '{$name}' добавлен!",
            ]);
        }

        // 1. Список фильмов
        if ($text === '/list') {
            $movies = $user->movies()->get();

            if ($movies->isEmpty()) {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'У вас пока нет фильмов.',
                ]);
            } else {
                $list = $movies->map(fn($m) => "#{$m->id}: {$m->name}")->implode("\n");
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Ваши фильмы:\n\n$list",
                ]);
            }
            return;
        }

        // 2. Показать фильм по ID
        if (str_starts_with($text, '/show ')) {
            $id = (int) substr($text, 6);
            $movie = $user->movies()->find($id);

            if (!$movie) {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Фильм с ID $id не найден.",
                ]);
                return;
            }

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Фильм #{$movie->id}\nНазвание: {$movie->name}\nПросмотры: {$movie->views}",
            ]);
            return;
        }

        // 3. Обновить фильм
        if (str_starts_with($text, '/update ')) {
            [$id, $newName] = explode(' ', substr($text, 8), 2);
            $movie = $user->movies()->find((int)$id);

            if (!$movie || !$newName) {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Неверный ID или пустое новое имя.",
                ]);
                return;
            }

            $movie->update(['name' => $newName]);

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Фильм #{$movie->id} обновлён. Новое название: {$movie->name}",
            ]);
            return;
        }

        // 4. Удалить фильм
        if (str_starts_with($text, '/delete ')) {
            $id = (int) substr($text, 8);
            $movie = $user->movies()->find($id);

            if (!$movie) {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Фильм с ID $id не найден.",
                ]);
                return;
            }

            $movie->delete();

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Фильм #{$id} успешно удалён.",
            ]);
            return;
        }

        if ($text === '/help') {
            $helpText = <<<TEXT
        Доступные команды:
        
        /add НазваниеФильма — добавить фильм  
        /list — список ваших фильмов  
        /show ID — показать фильм по ID  
        /update ID НовоеНазвание — обновить название фильма  
        /delete ID — удалить фильм  
        /help — показать это сообщение
        
        TEXT;
        
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $helpText,
            ]);
            return;
        }
        

    }
}