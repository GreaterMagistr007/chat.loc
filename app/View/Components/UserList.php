<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class UserList extends Component
{
    public $users;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $userId = Auth::user() ? Auth::user()->id : null;
        $users = User::where('id', '<>', $userId)->get();
        $this->users = [];
        foreach ($users as $user) {
            $this->users[] = [
                'key' => $user->id,
                'value' => $user->name,
            ];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-list');
    }
}
