<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['message_id', 'name', 'content'];

    public static function getFilesByMessageId($messageId) {
        $messageId = (int)$messageId;

        $files = DB::table('files')->where('message_id', $messageId)->get([
            'id',
            'message_id',
            'name',
        ]);

        foreach ($files as $key => $file) {
            $message = Message::where('id', $file->message_id)->first();
            $messageId = 0;
            $chatId = 0;
            if ($message) {
                $messageId = $message->id;
                $chatId = $message->getChatId();
            }

            $files[$key]->download_href = route('downloadFile', [
                'chatId' => $chatId,
                'messageId' => $messageId,
                'fileId' => $file->id,
            ]);
        }

        return $files;
    }

}
