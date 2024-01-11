<?php

namespace App\Http\Controllers;

use App\Models\Chat;
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

    public function chat($id)
    {
        return view('chat');
    }
}
