<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    @if (!count($chats))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    нет активных чатов
                </div>
            </div>
    @else
        @foreach($chats as $chat)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
        @endforeach
    @endif
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <x-button-href href="{!! route('get_createNewChat') !!}">Создать новый чат</x-button-href>
        </div>
    </div>
</div>
