<x-app-layout>
    <!-- Подключение CryptoJS -->
    <script src="https://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/aes.js"></script>
    <!-- Новая ссылка на GitHub -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>

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
                <x-button-href id="deleteChatButton" href="#" class="">Удалить чат</x-button-href>

                <div>
                    <x-input-label for="pass_key" :value="__('Ключ шифрования')" />
                    <x-text-input id="pass_key" name="pass_key" type="text" class="mt-1 block w-full" :value="__('Ключ шифрования')" required />

                    <script>
                        let sharedKey = document.querySelector('#pass_key').value;
                    </script>
                </div>
            </div>
        </div>
    </div>

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
                        <input type="file" multiple name="files[]"
                               class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                </form>

                <script>
                    let scrolling_element = document.getElementById('scrolling_element');
                    class Message {
                        is_renderer = false;
                        this_user_id = {!! $user->id !!};
                        block;

                        constructor(obj = {}, parentElement) {
                            for (let i in obj) {
                                this[i] = obj[i];
                            }
                            this.parentElement = parentElement;

                            this.render();
                        }

                        is_receiver() {
                            return parseInt(this.this_user_id) === parseInt(this.from_id);
                        }

                        getText() {
                            let text = this.text && this.text !== null ? this.text : '';

                            // console.log('this.text: ' + this.text);

                            text = CryptoJS.AES.decrypt(text, sharedKey).toString(CryptoJS.enc.Utf8);

                            // console.log('text: ' + text);

                            if (this.files && this.files.length) {
                                for (let i in this.files) {
                                    text += `
                                    <br>
                                    <a href="${this.files[i].download_href}" download="" style="display: inline-flex;">
                                        <img src="/file_icon.png" alt="${this.files[i].name}" style="    max-width: 20px;">
                                        ${this.files[i].name}
                                    </a>
                                    `;
                                }
                            }

                            return text;
                        }

                        render () {

                            // if(this.is_renderer) {
                            //     return;
                            // };
                            let text = this.getText();

                            // console.log('Рендерим сообщение: ' + text);
                            // console.log('sharedKey:' + sharedKey);


                            // console.log(this.block);

                            if (!text.length) {
                                if (this.block) {
                                    this.block.style.display = 'none';
                                }
                                this.is_renderer = true;
                                return;
                            }

                            if (this.block) {
                                this.block.style.display = 'table-row';
                            }




                            // let template = `
                            // <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >
                            //     <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white ${this.is_receiver() ? 'text-right' : ''}">
                            //         ${this.getText()}
                            //     </td>
                            // </tr>`;
                            // this.parentElement.innerHTML += template;

                            let template = `
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white ${this.is_receiver() ? 'text-right' : ''}">
                                    ${this.getText()}
                                </td>
                            `;

                            if (!this.block) {
                                this.block = document.createElement('tr');
                                this.block.classList.add("bg-white", "border-b", "dark:bg-gray-800", "dark:border-gray-700");

                                this.parentElement.appendChild(this.block);
                            }

                            this.block.innerHTML = template;

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

                            this.messageForm.addEventListener('keydown', function(e){
                                if (event.key === "Enter") {
                                    e.preventDefault()
                                    return self.sendMessage();
                                }
                            });

                            this.messageForm.querySelector('[name="message_text"]').focus();

                            document.querySelectorAll('#deleteChatButton').forEach(function(el){
                                el.addEventListener('click', function(e){
                                    let uri = `/chat/${self.id}/delete`;
                                    if (confirm('Удалить этот чат?')) {
                                        self.postQuery(uri, null, function(data){
                                            window.location.reload();
                                        });
                                    }

                                });
                            });

                            document.querySelector('#pass_key').addEventListener('keyup', function(e){
                                sharedKey = e.target.value;
                                // console.log('Ключ теперь: ' + sharedKey);
                                self.renderAllMessages();
                            });
                        }

                        renderAllMessages()
                        {
                            for (let i in this.messages) {
                                this.messages[i].render();
                            }
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

                        getMaxMessageId()
                        {
                            let result = 0;
                            for (let i in this.messages) {
                                let index = parseInt(i);
                                if (index > result) {
                                    result = index;
                                }
                            }

                            return result;
                        }

                        getNewMessages()
                        {
                            let uri = `/chat/${this.id}/new`;
                            let self = this;

                            let myFormData = new FormData();
                            myFormData.append('maxMessageId', this.getMaxMessageId());

                            this.postQuery(uri, myFormData, function(data){
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
                            let text = formData.get('message_text');
                            let newText = CryptoJS.AES.encrypt(text, sharedKey).toString();
                            // console.log('newText: ' + newText);
                            formData.set('message_text', newText);
                            let uri = `/chat/${this.id}/message`;

                            this.postQuery(uri, formData, function(data){
                                self.messageForm.reset();
                                self.messageForm.querySelector('[name="message_text"]').focus();
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

                                    if (data.error) {
                                        window.location.reload();
                                    }

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
                                    } else {
                                        // window.location.reload();
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

