<?php

namespace App\View\Components;

use App\Models\Chat;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Chats extends Component
{

    public $chats;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $userId = Auth::user() ? Auth::user()->id : null;
        $this->chats = Chat::getUserChats($userId);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chats');
    }
}
