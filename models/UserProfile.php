<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $f
 * @property string|null $i
 * @property string|null $o
 * @property string|null $tel
 * @property string|null $sub_tel
 * @property string|null $email
 * @property string|null $work_time
 */
class UserProfile extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'f', 'i', 'o', 'tel', 'sub_tel', 'email', 'work_time'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['f', 'i', 'o', 'tel', 'sub_tel', 'email', 'work_time'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID пользователя',
            'f' => 'Фамилия',
            'i' => 'Имя',
            'o' => 'Отчество',
            'tel' => 'Телефон',
            'sub_tel' => 'Добавочный',
            'email' => 'Email',
            'work_time' => 'Часы работы',
        ];
    }

}
