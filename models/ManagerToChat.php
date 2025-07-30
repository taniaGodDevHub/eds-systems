<?php

namespace app\models;

use MongoDB\Driver\Manager;
use Yii;

/**
 * This is the model class for table "manager_to_chat".
 *
 * @property int $id
 * @property int $manager_id
 * @property int $chat_id
 * @property int $client_id
 */
class ManagerToChat extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager_to_chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['manager_id', 'chat_id'], 'required'],
            [['manager_id', 'chat_id', 'client_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'manager_id' => 'Manager ID',
            'chat_id' => 'Chat ID',
        ];
    }

    public function getManagerProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'manager_id']);
    }

    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id']);
    }
}
