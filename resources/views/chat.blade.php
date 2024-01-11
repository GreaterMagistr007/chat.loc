<?php
$userId = \Illuminate\Support\Facades\Auth::user()->id;
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Чаты
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if (isset($message) && count($message))
                    @foreach($message as $m)
                        <x-message :message="$m" :is_receiver="$m->from_id === $userId">
                            текст сообщения 1
                        </x-message>
                    @endforeach
                @else
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        в этом чате нет сообщений
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



        </div>
    </div>

</x-app-layout>

