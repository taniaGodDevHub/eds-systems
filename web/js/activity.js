
class Activity{

    last_send_activity
    send_activity_interval
    last_activity_time
    check_interval
    check_user_activity_period
    constructor() {
        this.last_send_activity = false
        this.send_activity_interval = 5000
        this.last_activity_time = Date.now()// Переменная для хранения времени последней активности
        this.check_user_activity_period = 900000 //Интервал проверки на не активность. 15мин
        this.check_interval = setInterval(this.checkUserActivity, this.check_user_activity_period); //Проверяем на не активность

        this.event()
    }

    event(){
        // Обработчики событий для регистрации активности
        document.addEventListener('mousedown', () => this.updateLastActivityTime());
        document.addEventListener('scroll', () => this.updateLastActivityTime());
        document.addEventListener('keydown', () => this.updateLastActivityTime());
        document.addEventListener('touchstart', () => this.updateLastActivityTime()); // для мобильных устройств

        // Завершение проверки активности при закрытии вкладки или окна
        window.onbeforeunload = () => {
            clearInterval(this.checkInterval);
        };
    }

    // Функция проверки активности пользователя
    checkUserActivity() {
        const currentTime = Date.now();
        if (currentTime - this.last_activity_time > this.check_user_activity_period) { // проверка не активности
            console.log("Пользователь неактивен");
            clearInterval(this.check_interval); // остановим проверку, если пользователь ушел
        }
    }

    // Функция обновления времени последней активности
    updateLastActivityTime() {
        this.last_activity_time = Date.now();
        let currentTime = Date.now();
        let self = this
        if ((currentTime - this.last_send_activity) > this.send_activity_interval) {
            $.ajax(
                {
                    url: config.indexUrl + '?r=activity/set-active&user_id=' + user_id,
                    method: 'GET',
                    success: function (data) {
                        //console.log(data)
                        self.last_send_activity = Date.now()
                    },
                    error: function (error) {
                        console.error(error)
                    }
                }
            )
        }

        this.startCheckIntervalIfStopped(); // запускаем интервал снова, если он ранее остановился
    }

    // Функция запуска интервала проверки активности
    startCheckIntervalIfStopped() {
        if (!this.check_interval) {
            this.check_interval = setInterval(this.checkUserActivity, this.check_user_activity_period);
        }
    }
}

let activity = new Activity()








