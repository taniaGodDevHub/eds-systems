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
 * @property int $status_id
 */
class ManagerToChat extends \yii\db\ActiveRecord
{

    public $has_new;
    public $last_activity;

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
            [['manager_id', 'chat_id', 'client_id', 'status_id'], 'integer'],
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
            'status_id' => 'Chat status ID',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        // После загрузки каждой строки устанавливаем значение свойства
        $this->has_new = ChatMessage::find()
            ->where(['chat_id' => $this->chat_id])
            ->andWhere(['date_read' => null])
            ->andWhere(['author_id' => null])
            ->exists();

        $last = ChatMessage::find()
            ->select('date_add')
            ->where(['chat_id' => $this->chat_id])
            ->orderBy(['date_add' => SORT_DESC])
            ->one();
        $this->last_activity = !empty($last) ? $last->date_add : null;

    }

    public function getManagerProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'manager_id']);
    }

    public function getManager()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_id']);
    }

    public function getClient(){
        return $this->hasOne(Client::className(), ['chat_id' => 'chat_id']);
    }


    public function getMessages()
    {
        return $this->hasMany(ChatMessage::className(), ['chat_id' => 'chat_id'])->orderBy(['date_add' => SORT_DESC]);
    }
    public function getFirstTwoMessages()
    {
        return $this->hasMany(ChatMessage::className(), ['chat_id' => 'chat_id'])->orderBy(['date_add' => SORT_ASC])->limit(2);
    }

}
