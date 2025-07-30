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
        ob_flush(); flush();
        $this->telegram = Yii::$app->telegram;

        $postData = json_decode(file_get_contents('php://input'));
        Yii::info("Вебхук от ТG" . print_r($this->request->get(), true). print_r($postData,true), 'tg');

        if(isset($this->telegram->input->message->text)){
            $this->command = $this->telegram->input->message->text;
            $this->chat_id = $this->telegram->input->message->chat->id;
            $this->username = $this->telegram->input->message->chat->username;
            $this->clientFirstName = $this->telegram->input->message->chat->first_name;
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
    }

    private function selectManager()
    {
        $managers = User::find()
            ->joinWith('role')
            ->where(['auth_assignment.item_name' => 'user'])
            ->andWhere(['not', ['tg_id' => null]])
            ->all();

        if(empty($managers)){

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

        $minValue = min($counts);
        $keyOfMinValue = array_keys($counts, $minValue)[0];

        $newMTC = new ManagerToChat();
        $newMTC->chat_id = $this->chat_id;
        $newMTC->manager_id = $keyOfMinValue;
        $newMTC->save();

        $newManager = UserProfile::find()
            ->where(['user_id' => $newMTC->manager_id])
            ->one();

        $newManagerUser = User::findOne(['id' => $newMTC->manager_id]);

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Ваш менеджер: \n
    ".$newManager->f." ".$newManager->i." ".$newManager->o." \n
    Телефон: ".$newManager->tel." ".(!empty($newManager->sub_tel) ? " доб. ".$newManager->sub_tel : '')." \n
    Email: ".$newManager->email." \n
    Часы работы: ".$newManager->work_time ."\n 
    
    Он уже на связи в этом чате."
        ]);

        $this->telegram->sendMessage([
            'chat_id' => $newManagerUser->tg_id,
            'text' => "Новый клиент: " . $this->clientFirstName
        ]);
        exit();
    }

    /**
     * Связывает аккаунт пользователя с аккаунтом ТГ
     * @return void
     * @throws \yii\db\Exception
     */
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
            exit();
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
        exit();
    }

    /**
     * Отвечает, что не знает такой команды.
     * @return void
     */
    public function unknown()
    {
        //Определяем роль написавшего
        if(User::find()->where(['tg_id' => $this->chat_id])->exists()){
            //Это менеджер
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "менеджер"
            ]);
        }else{
            //Это клиент
            $issetManager = ManagerToChat::find()
                ->where(['chat_id' => $this->chat_id])
                ->with('managerProfile')
                ->one();

            if(empty($issetManager)){
                $this->selectManager();
            }

            $this->telegram->sendMessage([
                'chat_id' => $issetManager->manager_id,
                'text' => $this->command. "aaa"
            ]);
        }




    }

}
