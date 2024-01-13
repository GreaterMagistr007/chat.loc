<x-app-layout>
    <?php
        $timeToClose = $chat->getTimeToClose();
        ?>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Чат с пользователем {!! $chat->getAnotherUser()->name !!}<br>
            <small id="timeToClose" style="font-size: 60%;"></small>
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



            <form id="messageForm">
                <label for="chat" class="sr-only">Текст сообщения</label>
                <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <textarea id="chat" name="message_text" rows="1" class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Текст сообщения"></textarea>
                    <button type="submit" class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>
                        </svg>
                        <span class="sr-only">Отправить</span>
                    </button>
                </div>
                <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <input type="file" multiple name="files"
                    class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
            </form>

            <script>
                class Message {

                }

                class Chat {
                    messages = {};
                    id;
                    messageForm;
                    constructor()
                    {
                        this.id = {!! $chat->id !!};
                        this.messageForm = document.getElementById('messageForm');
                    }

                    getMessages()
                    {
                        let uri = `/chat/${this.id}/all`;
                        this.postQuery(uri, null, function(data){
                            console.log('data:', data);
                        });
                    }

                    sendMessage()
                    {
                        let formData = new FormData(this.messageForm);
                        let uri = `/chat/${this.id}/message`;

                        this.postQuery(uri, formData, function(data){
                            this.messageForm.reset();
                            console.log('data:', data);
                        });
                    }

                    postQuery(uri = '', formData = null, successCallback = null, errorCallback = null)
                    {
                        const options = {
                            method: 'POST',
                            headers: {
                                // 'Content-Type': 'application/json', // Устанавливаем заголовок для JSON-данных
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            },
                            body: formData, // Преобразуем данные в формат JSON
                        };

                        fetch(uri, options)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Ошибка сети: ${response.status}`);
                                }
                                return response.json(); // Возвращаем JSON из ответа, если необходимо
                            })
                            .then(data => {
                                console.log('Успешный ответ:', data);
                                if (successCallback) {
                                    successCallback(data);
                                }
                                // Дополнительные действия с данными, если необходимо
                            })
                            .catch(error => {
                                console.error('Ошибка:', error);
                                if (errorCallback) {
                                    errorCallback(error);
                                }
                                // Обработка ошибок, если необходимо
                            });
                    }
                }

                let chat = new Chat();
            </script>



        </div>
    </div>

    <script>
        let secondsToClose = {!! $timeToClose['fullSeconds'] !!};

        function getTimeToClose()
        {
            let hours = 0;
            let minutes = parseInt(secondsToClose / 60);
            while(minutes > 60) {
                minutes = minutes - 60;
                hours += 1;
            }
            let seconds = secondsToClose - (hours * 60 * 60) - (minutes * 60);

            return {
                'hours': hours,
                'minutes': minutes,
                'seconds': seconds,
            };
        }

        function toDoubleChars(int)
        {
            int = parseInt(int);
            if (int === 0) {
                return '00';
            }

            if (int > 9) {
                return int;
            }

            return '0' + int;
        }

        function renderTimeToClose()
        {
            let timeToClose = getTimeToClose();

            let text = toDoubleChars(timeToClose.hours) + ':' +
                toDoubleChars(timeToClose.minutes) + ':' +
                toDoubleChars(timeToClose.seconds)
            ;

            document.getElementById('timeToClose').innerHTML = 'Закроется через ' + text;
        }

        let timerId = setInterval(function () {
            renderTimeToClose();
            secondsToClose = secondsToClose - 1;

            if (secondsToClose < 1) {
                window.location.reload();
            }
        }, 1000);
    </script>

</x-app-layout>

