<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'from_id',
        'id_to',
        'text',
        'is_delivered',
    ];

    public static function getByChatId($chatId)
    {
        $chatId = (int)$chatId;
        $items = self::where('chat_id', $chatId)->orderBy('id')->get();

        foreach ($items as $item) {
            $item->setDelivered();
            $item->files = $item->getFiles();
        }

        return $items;
    }

    public static function getNewMessagesByChatId($chatId, $maxMessageId = 0)
    {
        $chatId = (int)$chatId;
        $result = self::where('chat_id', $chatId)->where('is_delivered', false)->where('id', '>', $maxMessageId)->orderBy('id')->get();

        foreach ($result as $item) {
            $item->setDelivered();
            $item->files = $item->getFiles();
        }

        return $result;
    }

    public function getFiles()
    {
        return File::getFilesByMessageId($this->id);
    }

    public function setDelivered()
    {
        try {
            if (Auth::user()->id !== $this->id_to ) {
                return;
            }

            $this->is_delivered = true;
            $this->save();
        } catch (\Exception $e) {

        }

    }

    public static function deleteAllByChatId($chatId)
    {
        $chatId = (int)$chatId;
        $messages = self::where('chat_id', $chatId)->get();
        foreach ($messages as $message) {
            $message->deleteFiles();
            $message->delete();
        }
    }

    public function deleteFiles()
    {
        File::where('message_id', $this->id)->delete();
    }

    public function getFileById($fileId)
    {
        $fileId = (int)$fileId;
        return File::where('id', $fileId)->where('message_id', $this->id)->first();
    }

    public function getChat()
    {
        return Chat::getById($this->chat_id);
    }

    public function getChatId()
    {
        $chat = $this->getChat();
        return $chat ? $chat->id : 0;
    }
}
