<?php

namespace app\controllers;


use app\models\ManagerToChat;
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
        $this->telegram = Yii::$app->telegram;

        $postData = json_decode(file_get_contents('php://input'));
        Yii::info("Вебхук от ТG" . print_r($this->request->get(), true). print_r($postData,true), 'tg');

        if(isset($this->telegram->input->message->text)){
            $this->command = $this->telegram->input->message->text;
            $this->chat_id = $this->telegram->input->message->chat->id;
            $this->username = $this->telegram->input->message->chat->username;
        }else{
            return true;
        }
        switch ($this->command){
            case '/start':

                $this->selectManager();
                break;
            case '/connect':

                $this->connect_user($this->username, $this->chat_id);
                break;
            default:

                $this->unknown();

                break;
        }

        return true;
    }

    private function selectManager()
    {
        $managers = User::find()
            ->joinWith('role')
            ->where(['auth_assignment.item_name' => 'user'])
            ->all();

        if(empty($managers)){

            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Сейчас нет ни одного менеджера в системе.",
            ]);
            return;
        }

        $issetManager = ManagerToChat::find()
            ->where(['chat_id' => $this->chat_id])
            ->with('managerProfile')
            ->one();

        if(!empty($issetManager)){
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Вам уже назначен менеджер: \n
    ".$issetManager->managerProfile->f." ".$issetManager->managerProfile->i." ".$issetManager->managerProfile->o." \n
    Телефон: ".$issetManager->managerProfile->tel." ".(!empty($issetManager->managerProfile->sub_tel) ? " доб. ".$issetManager->managerProfile->sub_tel : '')." \n
    Email: ".$issetManager->managerProfile->email." \n
    Часы работы: ".$issetManager->managerProfile->work_time
            ]);
            return;
        }

        //Выбираем менеджера с минимальным количеством чатов
        $counts = [];

        foreach($managers as $manager){
            $counts[$manager->id] = ManagerToChat::find()->count();;
        }

        $minValue = min($counts);
        $keyOfMinValue = array_keys($counts, $minValue)[0];

        $newMTC = new ManagerToChat();
        $newMTC->chat_id = $this->chat_id;
        $newMTC->manager_id = $keyOfMinValue;
        $newMTC->save();

        $newManager = UserProfile::find()
            ->where(['user_id' => $newMTC->manager_id])
            ->one();

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Вам назначен менеджер: \n
    ".$newManager->managerProfile->f." ".$newManager->managerProfile->i." ".$newManager->managerProfile->o." \n
    Телефон: ".$newManager->managerProfile->tel." ".(!empty($newManager->managerProfile->sub_tel) ? " доб. ".$newManager->managerProfile->sub_tel : '')." \n
    Email: ".$newManager->managerProfile->email." \n
    Часы работы: ".$newManager->managerProfile->work_time ."\n Он уже на связи в этом чате."
        ]);
        return;


    }

    public function connect_user()
    {
        $user = User::find()
            ->where(['tg_login' => $this->username])
            ->one();

        if(empty($user)){
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Не найден пользователь с таким логином телеграм. Добавьте логин тг в настройках профиля пользователя.",
            ]);
            return;
        }

        $user->tg_id = (string)$this->chat_id;

        if(!$user->save()){
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "Данные не сохранились. Попробуйте ещё раз или напишите с техническую поддержку.".print_r($user->getErrors(),true),
            ]);
        }
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Всё сработало. Ваша учётная запись связана с этим чатом"
        ]);
        return true;
    }

    public function unknown()
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "К сожалению я не знаю такой команды. Я умею пока не много но быстро учусь. Что я могу для вас сделать? " . $this->chat_id,
        ]);
    }
}
