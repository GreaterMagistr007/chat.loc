<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return self::where('chat_id', $chatId)->orderBy('id')->get();
    }

    public static function getNewMessagesByChatId($chatId)
    {
        $chatId = (int)$chatId;
        $result = self::where('chat_id', $chatId)->where('is_delivered', false)->orderBy('id')->get();

        foreach ($result as $item) {
            $item->setDelivered();
        }

        return $result;
    }

    public function setDelivered()
    {
        $this->is_delivered = true;
        $this->save();
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
}
