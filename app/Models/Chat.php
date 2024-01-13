<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_di1',
        'user_di2',
    ];

    /**
     * @param $userId
     * @return mixed
     */
    public static function getUserChats($userId)
    {
        $userId = (int)$userId;
        self::deleteOld();
        return self::where('user_di1', $userId)->orWhere('user_di2', $userId)->get();
    }

    /**
     * Удаляем чаты, у которых со времени последнего изменения прошло больше установленного
     * @return void
     */
    public static function deleteOld(): void
    {
        $endTime = Carbon::now()->subMinutes(env('DELETE_CHAT_MINUTES'));
        self::where('updated_at', '<', $endTime)->delete();
    }

    public static function createNewChat($user1, $user2)
    {
        $item = new self([
            'user_di1' => $user1->id,
            'user_di2' => $user2->id,
        ]);

        $item->save();
        return $item;
    }

    public function getAnotherUser()
    {
        $user = Auth::user();

        $anotherUserId = (int)$this->user_di1 === $user->id ? (int)$this->user_di2 : (int)$this->user_di1;

        return User::where('id', $anotherUserId)->first();
    }

    public function isUserChat()
    {
        $user = Auth::user();

        return (int)$this->user_di1 === $user->id ||  (int)$this->user_di2 === $user->id;
    }

    public function getMessages()
    {
        return Message::getByChatId($this->id);
    }
}
