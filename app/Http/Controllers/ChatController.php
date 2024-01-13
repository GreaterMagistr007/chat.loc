<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\File;
use App\Models\Helper;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getCreateNewChat()
    {
        return view('create-new-chat');
    }

    public function postCreateNewChat(Request $request)
    {
        $userId = (int)$request->userid;
        $user2 = User::where('id', $userId)->first();
        if (!$user2) {
            return view('create-new-chat');
        }

        $user1 = Auth::user();

        $chat = Chat::createNewChat($user1, $user2);

        return redirect(route('getChat', ['id' => $chat]));

    }

    public function getChat($id)
    {
        /** @var Chat $chat */
        $chat = Chat::getChatForThisUserById($id);
        if (!$chat) {
            return view('chat_not_available');
        }

        $params = [
            'user' => Auth::user(),
            'chat' => $chat,
            'messages' => $chat->getMessages()
        ];

        return view('chat', $params);
    }

    public function postAllMessages($id)
    {
        /** @var Chat $chat */
        $chat = Chat::getChatForThisUserById($id);
        if (!$chat) {
            return self::error('К этому чату нет доступа');
        }

        return self::success('', ['messages' => $chat->getMessages()]);
    }

    public function postSendMessage($id)
    {
        $request = request();
        $text = $request->message_text;

        /** @var Chat $chat */
        $chat = Chat::getChatForThisUserById($id);
        if (!$chat) {
            return self::error('К этому чату нет доступа');
        }

        $uploadedFiles = $request->files;

        if (!$text && !$uploadedFiles) {
            return self::success('');
        }

        $message = $chat->sendMessage($text);

        foreach ($uploadedFiles as $files) {
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $content = file_get_contents($file->getRealPath());
                File::create([
                    'message_id' => $message->id,
                    'name' => $originalName,
                    'content' => $content,
                ]);
            }
        }

        return self::success('');
    }

    public function postGetNewMessages($id)
    {
        /** @var Chat $chat */
        $chat = Chat::getChatForThisUserById($id);
        if (!$chat) {
            return self::error('К этому чату нет доступа');
        }

        $maxMessageId = (int)request()->maxMessageId;

        return self::success('', ['messages' => $chat->getNewMessages($maxMessageId)]);
    }

    public function downloadFile($chatId, $messageId, $fileId)
    {
        /** @var Chat $chat */
        $chat = Chat::getChatForThisUserById($chatId);
        if (!$chat) {
            return self::error('К этому чату нет доступа');
        }

        /** @var Message $message */
        $message = $chat->getMessageById($messageId);
        if (!$message) {
            return self::error('К этому сообщению нет доступа');
        }

        /** @var File $file */
        $file = $message->getFileById($fileId);
        if (!$file) {
            return self::error('К этому файлу нет доступа');
        }

        return response()->stream(
            function () use ($file) {
                echo $file->content;
            },
            200,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $file->name . '"',
            ]
        );
    }
}
