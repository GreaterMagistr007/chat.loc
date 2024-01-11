<form class="py-12" method="post" action="{!! route('post_createNewChat') !!}">
    @csrf
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <x-select name="userid" :items="$users" :required="true">Выберите пользователя</x-select>

            </div>

            <div class="p-6 text-gray-900 dark:text-gray-100">
                <x-primary-button>
                    Создать
                </x-primary-button>
            </div>

        </div>
    </div>


</form>
