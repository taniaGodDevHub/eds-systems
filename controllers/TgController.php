<?php

namespace app\controllers;


use app\models\ChatMessage;
use app\models\Client;
use app\models\ManagerToChat;
use app\models\TgMessage;
use app\models\User;
use app\models\UserProfile;
use Yii;

class TgController extends AccessController
{
    public $enableCsrfValidation = false;

    public $telegram = false;
    public $chat_id = false;
    public $username = false;
    public $command = false;
    public $clientFirstName = false;
    public $clientLastName = false;
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['Authorization'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        header('HTTP/1.1 200 OK');
        if(ob_get_level()) {
            ob_flush();
            flush();
        }

        $this->telegram = Yii::$app->telegram;

        $postData = json_decode(file_get_contents('php://input'));
        Yii::info("Вебхук от ТG" . print_r($this->request->get(), true). print_r($postData,true), 'tg');

        if(isset($this->telegram->input->message->text)){
            Yii::info("Сообщение не пустое", 'tg');
            $this->command = $this->telegram->input->message->text;
            $this->chat_id = $this->telegram->input->message->chat->id;
            $this->username = $this->telegram->input->message->chat->username;
            $this->clientFirstName = $this->telegram->input->message->chat->first_name;
        }else{
            return true;
        }

        /*$tgMessage = new TgMessage();
        $tgMessage->chat_id = $this->chat_id;
        $tgMessage->message_id = $this->telegram->input->message->message_id;
        $tgMessage->author_id = $this->telegram->input->message->from->id;
        $tgMessage->text = $this->command;
        $tgMessage->save();*/

        switch ($this->command){
            case '/start':
                Yii::info("Выбираем менеджера", 'tg');
                $this->selectManager();
                break;
            case '/connect':

                Yii::info("Коннектим", 'tg');
                $this->connect_user($this->username, $this->chat_id);
                break;
            default:

                Yii::info("Просто сообщение", 'tg');
                $this->unknown();

                break;
        }
    }

    private function selectManager($client_chat_id = false)
    {
        Yii::info("Входящий client_chat_id: $client_chat_id", 'tg');
        Yii::info("Текущий chat_id $this->chat_id", 'tg');

        $managers = User::find()
            ->joinWith('role')
            ->where(['auth_assignment.item_name' => 'admin'])
            ->andWhere(['not', ['tg_id' => null]])
            ->all();

        if(empty($managers)){
            Yii::info("Менеджеров нет", 'tg');
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Сейчас нет ни одного менеджера в системе.",
            ]);
            exit();
        }

        $issetManager = ManagerToChat::find()
            ->where(['chat_id' => $this->chat_id])
            ->with('managerProfile')
            ->one();

        if(!empty($issetManager)){
            Yii::info("Менеджер уже назначен", 'tg');
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Вам уже назначен менеджер: \n
    ".$issetManager->managerProfile->f." ".$issetManager->managerProfile->i." ".$issetManager->managerProfile->o." \n
    Телефон: ".$issetManager->managerProfile->tel." ".(!empty($issetManager->managerProfile->sub_tel) ? " доб. ".$issetManager->managerProfile->sub_tel : '')." \n
    Email: ".$issetManager->managerProfile->email." \n
    Часы работы: ".$issetManager->managerProfile->work_time." \n
    Вы можете написать ему в этом чате"
            ]);
            exit();
        }

        //Выбираем менеджера с минимальным количеством чатов
        $counts = [];

        foreach($managers as $manager){
            $counts[$manager->id] = ManagerToChat::find()->count();;
        }

        $min = null;
        $min_id = null;
        foreach ($counts as $id => $count){

            if($count < $min || $min === null)
            {
                $min = $count;
                $min_key = $id;
            }
        }
        Yii::info("Выбрали менеджера с ИД $min_key", 'tg');

        $chat_id = $this->chat_id;
        Yii::info("chat_id перед записью менеджера в базу $chat_id", 'tg');
        $newMTC = new ManagerToChat();
        $newMTC->chat_id = $this->chat_id;
        $newMTC->manager_id = $min_key;
        $newMTC->client_id = $chat_id;
        if(!$newMTC->save()){
            Yii::info("Не удалось сохранить." . print_r($newMTC->getErrors(), true), 'tg');
        }else{
            Yii::info("Сохранили ", 'tg');
        }

        if(!Client::find()->where(['chat_id' => $this->chat_id])->exists()){
            Yii::info("Клиента ещё нет с ИД $client_chat_id. Создаём", 'tg');
            $client = new Client();
            $client->chat_id = $newMTC->chat_id;
            $client->f = $this->username;
            $client->i = $this->username;
            $client->o = $this->username;
            $client->date_add = time();

            if(!$client->save()){
                Yii::info("Не удалось сохранить." . print_r($client->getErrors(), true), 'tg');
            }
        }


        $newManager = UserProfile::find()
            ->where(['user_id' => $newMTC->manager_id])
            ->one();

        $newManagerUser = User::findOne(['id' => $newMTC->manager_id]);

        $msg = "Ваш менеджер: \n
".$newManager->f." ".$newManager->i." ".$newManager->o." \n
Телефон: ".$newManager->tel." ".(!empty($newManager->sub_tel) ? " доб. ".$newManager->sub_tel : '')." \n
Email: ".$newManager->email." \n
Часы работы: ".$newManager->work_time ."\n 

Он уже на связи в этом чате.";
        Yii::info("Отправляем сообщение клиенту с назначенным менеджером", 'tg');
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $msg
        ]);

        Yii::info("Записываем сообщение в базу", 'tg');
        $localMsg = new ChatMessage();
        $localMsg->chat_id = $this->chat_id;
        $localMsg->message = $msg;
        $localMsg->author_id = $newManager->user_id;
        $localMsg->date_add = time();
        $localMsg->date_send = time();
        $localMsg->user_chat_id = $client_chat_id;

        if(!$localMsg->save()){
            Yii::info("е удалось записать". print_r($localMsg->getErrors(), true), 'tg');
        }

        Yii::info("Отправляем сообщение менеджеру о новом клиенте", 'tg');

        $msg = "Новый клиент: " . $this->clientFirstName;
        $this->telegram->sendMessage([
            'chat_id' => $newManagerUser->tg_id,
            'text' => $msg
        ]);

        Yii::info("Записываем сообщение в базу", 'tg');
        $localMsg = new ChatMessage();
        $localMsg->chat_id = $this->chat_id;
        $localMsg->message = $msg;
        $localMsg->date_add = time();
        $localMsg->user_chat_id = $client_chat_id;
        if(!$localMsg->save()){
            Yii::info("е удалось записать". print_r($localMsg->getErrors(), true), 'tg');
        }


        exit();
    }

    /**
     * Связывает аккаунт пользователя с аккаунтом ТГ
     * @return void
     * @throws \yii\db\Exception
     */
    private function connect_user()
    {
        $user = User::find()
            ->where(['tg_login' => $this->username])
            ->one();

        if(empty($user)){
            Yii::info("Пользователь в базе не найден", 'tg');
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Не найден пользователь с таким логином телеграм. Добавьте логин тг в настройках профиля пользователя.",
            ]);
            exit();
        }

        $user->tg_id = (string)$this->chat_id;

        if(!$user->save()){
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Данные не сохранились. Попробуйте ещё раз или напишите с техническую поддержку.".print_r($user->getErrors(),true),
            ]);
        }

        Yii::info("Сконнектили менеджера. Отправляем сообщение", 'tg');

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Всё сработало. Ваша учётная запись связана с этим чатом"
        ]);
        exit();
    }

    /**
     * Отвечает, что не знает такой команды.
     * @return void
     */
    private function unknown()
    {
        //Определяем роль написавшего
        if(User::find()->where(['tg_id' => $this->chat_id])->exists()){
            Yii::info("Это сообщение от менеджера", 'tg');
            //Это менеджер
            //Если нет указание на то, что это ответ отправляем сообщение о том, что нужно именно ответить.
            if(!$this->telegram->input->message->reply_to_message){

                Yii::info("Не ответ. Просим отвечать", 'tg');
                $this->telegram->sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => "Сообщение не отправлено. Для отправки сообщения используйте функцию телеграм \"Ответить\""
                ]);
                exit();
            }

            $firstHashPos = strpos($this->telegram->input->message->reply_to_message['text'], '#');
            $secondHashPos = strrpos($this->telegram->input->message->reply_to_message['text'], '#');

// Извлекаем подстроку между первой и последней решеткой
            $result = substr($this->telegram->input->message->reply_to_message['text'], $firstHashPos + 1, $secondHashPos - $firstHashPos - 1);

            Yii::info("Подстрока с ИД чата клиента: $result", 'tg');

            if (empty($result)){

                Yii::info("Подстрока пуста. Отправляем сообщение менеджеру", 'tg');

                $this->telegram->sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => "Не удалось найти сообщение для ответа. Не найден тег клиента: " . print_r($result, true) ." Сообщение: " .$this->command
                ]);
                exit();
            }

            Yii::info("Отправляем сообщение клиенту", 'tg');
            $this->telegram->sendMessage([
                'chat_id' => (int)$result,
                'text' => $this->command
            ]);



        }else{
            Yii::info("Это клиент с chat_id ". $this->chat_id, 'tg');
            //Это клиент
            $issetManager = ManagerToChat::find()
                ->where(['chat_id' => $this->chat_id])
                ->with('manager')
                ->one();

            if(empty($issetManager)){
                Yii::info("У клиента нет менеджера. Идём в выбор", 'tg');
                $this->selectManager($this->chat_id);
            }
            Yii::info("У клиента есть менеджер", 'tg');

            if(!Client::find()->where(['chat_id' => $this->chat_id])->exists()){
                Yii::info("Клиента ещё нет с ИД $this->chat_id. Создаём", 'tg');
                $client = new Client();
                $client->chat_id = $issetManager->chat_id;
                $client->f = $this->username;
                $client->i = $this->username;
                $client->o = $this->username;
                $client->date_add = time();

                if(!$client->save()){
                    Yii::info("Не удалось сохранить." . print_r($client->getErrors(), true), 'tg');
                }
            }

            Yii::info("Сохраняем сообщение в базу", 'tg');
            $localMsg = new ChatMessage();
            $localMsg->chat_id = $issetManager->manager->tg_id;
            $localMsg->message = $this->command;
            $localMsg->date_add = time();
            $localMsg->user_chat_id = $this->chat_id;
            $localMsg->save();

            Yii::info("Пересылаем сообщение менеджеру", 'tg');
            $this->telegram->sendMessage([
                'chat_id' => $issetManager->manager->tg_id,
                'text' => $this->command. "\nКлиент: #$this->chat_id#"
            ]);
        }




    }

}
