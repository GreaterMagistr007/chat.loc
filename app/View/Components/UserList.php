<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class UserList extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $userId = Auth::user() ? Auth::user()->id : null;
        $this->users = User::where('id', '<>', $userId)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-list');
    }
}
