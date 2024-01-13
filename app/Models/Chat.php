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

    public static function getById($id)
    {
        $id = (int)$id;
        self::deleteOld();

        return self::where('id', $id)->first();
    }

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
        $endTime = Carbon::now()->subMinutes(env('DELETE_CHAT_MINUTES'))->timestamp;
        $chats = self::where('updated_at', '<', $endTime)->get();

        /** @var Chat $chat */
        foreach ($chats as $chat) {
            $chat->deleteAllMessages();
            $chat->delete();
        }
    }

    public function deleteAllMessages()
    {
        Message::deleteAllByChatId($this->id);
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

    public function getNewMessages($maxMessageId = 0)
    {
        return Message::getNewMessagesByChatId($this->id, $maxMessageId);
    }

    public function getTimeToClose()
    {
        $endTime = $this->updated_at->addMinutes(env('DELETE_CHAT_MINUTES'));
        $now = Carbon::now();

        $fullSecondsToClose = $now->diffInSeconds($endTime);

        $hours = 0;
        $minutes = intval($fullSecondsToClose / 60);
        while ($minutes > 60) {
            $minutes = $minutes - 60;
            $hours += 1;
        }
        $seconds = $fullSecondsToClose - ($hours * 60 * 60) - ($minutes * 60);


        $result = [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'fullSeconds' => $fullSecondsToClose
        ];

        return $result;
    }

    public static function getChatForThisUserById($chatId)
    {
        $chat = self::getById($chatId);
        if (!$chat || !$chat->isUserChat()) {
            return null;
        }

        return $chat;
    }

    public function sendMessage($text)
    {
        $from_id = Auth::user()->id;
        $anotherUser = $this->getAnotherUser();
        $id_to = $anotherUser ? $anotherUser->id : 0;
        $message = new Message([
            'chat_id' => $this->id,
            'from_id' => $from_id,
            'id_to' => $id_to,
            'text' => $text,
        ]);

        $message->save();
        return $message;
    }

    public function getMessageById($messageId)
    {
        $messageId = (int)$messageId;
        return Message::where('id', $messageId)->where('chat_id', $this->id)->first();
    }
}
