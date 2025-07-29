<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{

    public $username;
    public $email;
    public $password;
    public $role;
    public $tg_login;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Это имя пользователя уже используется'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот адрес уже используется.'],
            ['tg_login', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот логин уже используется'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['role', 'required'],
            ['role', 'string', 'max' => 30],
            ['role', function ($attribute, $params, $validator) {
                if ($this->$attribute !== 'user') {
                    $this->addError($attribute, 'Не хорошо пытаться взламывать чужие сайты!');
                }
            },  'skipOnEmpty' => false],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {

        if (!$this->validate()) {
            Yii::$app->session->setFlash('warning', "Что-то заполнено не верно.");

            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->tg_login = $this->tg_login;

        if(!$user->save()){
            Yii::$app->session->setFlash('warning', "Регистрация не удалась." . print_r($user->getErrors(), true));
            return null;
        }
        return $user;
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'email' => 'Email',
            'tg_login' => 'Логин в TG без @',
        ];
    }

}