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

    <div class="flex flex-col max-h-screen" >
        <div class="flex-grow" style="padding-top: 3rem">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



                <div id="scrolling_element" class="relative overflow-x-auto" >
                    <table  class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <tbody id="message_block">
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >

                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



                <form id="messageForm">
                    <label for="chat" class="sr-only">Текст сообщения</label>
                    <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                        <textarea id="chat" name="message_text" rows="1" class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Текст сообщения"></textarea>
                        <button id="send_message_button" type="submit" class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600">
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
                    let scrolling_element = document.getElementById('scrolling_element');
                    class Message {
                        is_renderer = false;
                        this_user_id = {!! $user->id !!};

                        constructor(obj = {}, parentElement) {
                            for (let i in obj) {
                                this[i] = obj[i];
                            }
                            this.parentElement = parentElement;

                            this.render();
                        }

                        is_receiver() {
                            return parseInt(this.this_user_id) === this.from_id;
                        }

                        render () {
                            if(this.is_renderer) {
                                return;
                            };
                            let template = `
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white ${this.is_receiver ? 'text-right' : ''}">
                                    ${this.text}
                                </td>
                            </tr>`;

                            this.parentElement.innerHTML += template ;
                            scrolling_element.scrollTop = scrolling_element.scrollHeight;

                            this.is_renderer = true;
                        }
                    }

                    class Chat {
                        messages = {};
                        id;
                        messageForm;
                        milisecondsTogetMessages = 700;
                        messagesBlock = document.querySelector('#message_block');
                        constructor()
                        {
                            let self = this;
                            this.id = {!! $chat->id !!};
                            this.messageForm = document.getElementById('messageForm');

                            this.getMessages();

                            setTimeout(function () {
                                self.getNewMessages()
                            }, self.milisecondsTogetMessages);

                            this.messageForm.addEventListener('submit', function(e){
                                e.preventDefault();
                                self.sendMessage();
                                self.messageForm.reset();
                            });
                        }

                        addMessageToStorage(message = {})
                        {
                            let self = this;

                            if (message.id && !self.messages[message.id]) {
                                self.messages[message.id] = new Message(message, self.messagesBlock);
                            }
                        }

                        getMessages()
                        {
                            let self = this;
                            let uri = `/chat/${this.id}/all`;
                            this.postQuery(uri, null, function(data){
                                data.messages.map((m) => {
                                    self.addMessageToStorage(m);
                                });
                            });
                        }

                        renderAllMessages()
                        {

                        }

                        getNewMessages()
                        {
                            let uri = `/chat/${this.id}/new`;
                            let self = this;

                            this.postQuery(uri, null, function(data){
                                setTimeout(function () {
                                    self.getNewMessages()
                                }, self.milisecondsTogetMessages);

                                data.messages.map((m) => {
                                    self.addMessageToStorage(m);
                                });
                            });
                        }

                        sendMessage()
                        {
                            let self = this;
                            let formData = new FormData(this.messageForm);
                            let uri = `/chat/${this.id}/message`;

                            this.postQuery(uri, formData, function(data){
                                self.messageForm.reset();
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
                                    // console.log('Успешный ответ:', data);
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
    </div>

    <script>
        let scrolling_element_for_set_height = document.querySelector('#scrolling_element');
        let availableHeight = (screen.availHeight - 500) + 'px';
        scrolling_element_for_set_height.style.maxHeight = availableHeight;
        scrolling_element_for_set_height.parentNode.style.maxHeight = availableHeight;
        scrolling_element_for_set_height.parentNode.parentNode.style.maxHeight = availableHeight;
    </script>


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

