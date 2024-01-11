<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getCreateNewChat()
    {
        return view('create-new-chat');
    }

    public function postCreateNewChat(Request $request)
    {
        $userId = (int)$request->userid;
        $user = User::where('id', $userId)->first();
        if (!$user) {
//            session()->
        }
    }
}
