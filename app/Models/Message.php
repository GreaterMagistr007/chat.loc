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
}
