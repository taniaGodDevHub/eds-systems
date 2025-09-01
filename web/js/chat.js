class Chat {

    can_send = true
    messages = []
    chat_list_item = false
    message_list_header = $('#message_list_header')
    message_list_header_client_name = $('#message_list_header_client_name')
    message_list_header_last_activity = $('#message_list_header_last_activity')
    message_list_header_close_btn_block = $('#message_list_header_close_btn_block')
    message_list_header_close_btn = $('#message_list_header_close_btn')
    message_list = $('#message_list')
    chat_text_input = $('#chat_text_input')
    chat_text_count = $('#chat_text_count')
    send_msg_btn = $('#send_msg_btn')
    lastMsgTime = 0
    chatMsgInterval = false

    /**
     * Создаёт стартовые слушатели
     */
    events(){
        let self = this
        this.chat_text_input.on('keyup', ()=> this.checkCount(this.chat_text_input.html()))
        his.chat_text_input.on('keyup', function(event) {
            if (event.key === 'Enter') {
                console.log('enter');
                if (event.ctrlKey) {
                    console.log('ctrl');
                    console.log('self.chat_text_input', self.chat_text_input);
                    console.log('self.chat_text_input.text()', self.chat_text_input.text());

                    // Текущее положение курсора
                    let selection = window.getSelection();
                    let range = selection.getRangeAt(0);

                    // Вставляем <br> там, где находился курсор
                    range.deleteContents();
                    let brNode = document.createElement('br');
                    range.insertNode(brNode);

                    // Возвращаем курсор после <br>
                    range.setStartAfter(brNode);
                    range.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(range);

                    event.preventDefault(); // Предотвратим стандартное поведение Enter
                } else {
                    // Обычный Enter: вызов функции отправки
                    event.preventDefault();
                    self.sendMsg();
                }
            }
        });

        console.log($(document))
        $(document).on('pjax:end', function() {
            window.location.href = config.indexUrl + '?r=chat/index'
        });

    }

    /**
     * Выполняет все действия при выборе активного чата
     * @param chat_id
     */
    async selectChat(chat_id) {

        console.log('-=selectChat=-')

        clearInterval(this.chatMsgInterval)

        this.lastMsgTime = 0

        this.message_list.html("")
        this.message_list.html(`<div class="d-flex justify-content-center mt-5">
          <div class="spinner-grow text-info" role="status">
            <span class="visually-hidden">Загрузка...</span>
          </div>
          <div class="spinner-grow text-info ms-3 me-3" role="status">
            <span class="visually-hidden">Загрузка...</span>
          </div>
          <div class="spinner-grow text-info" role="status">
            <span class="visually-hidden">Загрузка...</span>
          </div>
        </div>`)

        this.chat_list_item = $('#chat_list_item_' + chat_id)

        $('.chat-list-item').each(function() {
            $(this).removeClass('border-info');
        })
        this.chat_list_item.addClass('border-info')

        this.send_msg_btn.data('chat-id', chat_id)

        await this.getMessage(chat_id)

        this.updateListHeader()

        this.setRead(chat_id)

        this.chatMsgInterval = setInterval(async () => {

            await this.getMessage(chat_id)
        }, 5000)
    }

    updateMessageList() {
        console.log('-=updateMessageList=-')

        let html = ``
        let position, bg, date, read_class, last_msg_id
        for (const m of this.messages) {
            //console.log('this.lastMsgTime', this.lastMsgTime, 'm.date_add', m.date_add)
            if(this.lastMsgTime >= m.date_add){
                continue;
            }
            //console.log('m', m)
            position = m.author_id === null ? 'end' : 'start'
            bg = m.author_id === null ? 'info' : 'success'
            date = this.timestampToDate(m.date_add)
            last_msg_id = m.id
            this.lastMsgTime = m.date_add
            html += `
                <div id="chat_message_${m.id}" class="row justify-content-${position} mt-3">
                    <div class="col-8 card message-bg-${bg}">
                        <div class="card-body pb-1">
                            <div class="col-12">
                                ${m.message.replace(/\n/g, '<br>')}
                            </div>
                            <div class="col-12 text-end fw-lighter" style="font-size: 12px;">
                                ${date}
                            </div>
                        </div>
                    </div>
                </div>
            `
            this.message_list.append(html)
        }

        const element = document.querySelector('#chat_message_' + last_msg_id); // выбираем нужный блок
        if(element){
            element.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    }

    /**
     * Обновляет заголовок активного чата
     */
    updateListHeader() {
        console.log('-=updateListHeader=-')
        this.message_list_header_client_name.html(this.chat_list_item.data('client-name'))
        this.message_list_header_last_activity.html(this.chat_list_item.data('last-activity'))

        let close_btn = `
            <button 
                id="message_list_header_close_btn" 
                class="btn btn-danger btn-sm" 
                onclick="chat.closeChat('${this.chat_list_item.data('chat-id')}')"
                data-confirm="Чат исчезнет из вашего списка клиентов. История переписки не будет удалена. Закрыть чат?" 
            >
                Закрыть чат
            </button>
        `
        this.message_list_header_close_btn_block.html(close_btn)
    }

    /**
     * Получает сообщения чата
     * @param chat_id
     */
    getMessage(chat_id) {
        console.log('-=getMessage=-')
        console.log('chat_id', chat_id)
        let self = this
        $.ajax(
            {
                url: config.indexUrl + '?r=chat/get-messages&chat_id=' + chat_id,
                method: 'GET',
                success: function (data) {
                    //console.log(data)
                    self.messages = data
                    self.updateMessageList()
                },
                error: function (error) {
                    console.error(error)
                }
            }
        )
    }

    closeChat(chat_id){
        console.log('-=closeChat=-')
        let self = this
        $.ajax(
            {
                url: config.indexUrl + '?r=chat/close&id=' + chat_id,
                method: 'GET',
                success: function (data) {
                    console.log(data)
                    $('#chat_list_item_' + chat_id).remove()
                    self.message_list.html("")
                    self.message_list_header_client_name.html("")
                    self.message_list_header_last_activity.html("")
                    self.message_list_header_close_btn_block.html("")

                    let html = `
                    <div class="spinner-grow text-info spinner-grow-sm" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    Чат закрыт. Выберите чат с клиентом слева.
                    `
                },
                error: function (error) {
                    console.error(error)
                }
            }
        )
    }

    enableTitleNotify(){

        const originalTitle = document.title;
        const blinkInterval = setInterval(() => {
            const currentTitle = document.title;
            if (currentTitle === originalTitle) {
                document.title = '🔴 ' + 'Новое сообщение';
            } else {
                document.title = originalTitle;
            }
        }, 1000); // Интервал переключения каждые 1 секунду

        window.addEventListener('focus', () => {
            clearInterval(blinkInterval)
            document.title = originalTitle;
        });
    }

    /**
     * Помечает все сообщения в чате прочитанными
     * @param chat_id
     */
    setRead(chat_id) {
        console.log('-=setRead=-')

        let self = this
        $.ajax(
            {
                url: config.indexUrl + '?r=chat/set-read&chat_id=' + chat_id,
                method: 'GET',
                success: function (data) {
                    console.log(data)
                },
                error: function (error) {
                    console.error(error)
                }
            }
        )
    }

    /**
     * Проверяет кол-во символов в поле ввода и помечает превышения
     * @param str
     * @param max
     */
    checkCount(str, max = 4000){

        let currentLength = this.chat_text_input.html().length
        this.chat_text_count.html(currentLength + '/' + max)
        if(currentLength > max){
            this.can_send = false
            this.chat_text_count.addClass("text-danger")
            this.chat_text_count.html(currentLength + '/' + max)
            this.send_msg_btn.removeClass('text-info')
            this.send_msg_btn.addClass('text-danger')
        }else{
            this.can_send = true
            this.chat_text_count.removeClass("text-danger")
            this.send_msg_btn.removeClass('text-danger')
            this.send_msg_btn.addClass('text-info')
        }

    }

    sendMsg(){

        console.log('-=sendMsg=-')

        if(!this.can_send){
            this.send_msg_btn.removeClass('text-info')
            this.send_msg_btn.addClass('text-danger')
        }else {
            let self = this
            let chat_id = self.send_msg_btn.data('chat-id')

            $.ajax(
                {
                    url: config.indexUrl + '?r=chat/send-message',
                    method: 'POST',
                    data:{
                        _csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        message: self.chat_text_input.html(),
                        chat_id:chat_id
                    },
                    success: function (data) {
                        console.log(data)
                    },
                    error: function (error) {
                        console.error(error)
                    }
                }
            )

            this.chat_text_input.html("")
            this.getMessage(chat_id)
            this.updateMessageList()
        }
    }

    /**
     * Преобразует метку времени в дату
     * @param timestamp
     * @returns {string}
     */
    timestampToDate(timestamp){
        let dateObj = new Date(timestamp * 1000); // Умножаем на 1000, поскольку в JavaScript работает с миллисекундами

        return `${dateObj.getDate().toString().padStart(2, '0')} `
            + `.${(dateObj.getMonth()+1).toString().padStart(2, '0')} `
            + `.${dateObj.getFullYear()} `
            + ` ${dateObj.getHours().toString().padStart(2, '0')}:`
            + `${dateObj.getMinutes().toString().padStart(2, '0')}:`
            + `${dateObj.getSeconds().toString().padStart(2, '0')}`;
    }

}

let chat = new Chat();
chat.events()