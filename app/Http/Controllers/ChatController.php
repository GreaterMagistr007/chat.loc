<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getCreateNewChat()
    {
        return view('create-new-chat');
    }
}
