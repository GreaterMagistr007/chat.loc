<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

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
}