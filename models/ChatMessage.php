<?php

namespace app\models;

use aki\telegram\types\Chat;
use Yii;

/**
 * This is the model class for table "chat_message".
 *
 * @property int $id
 * @property int $chat_id
 * @property string $message
 * @property int|null $author_id
 * @property int|null $date_add
 * @property int|null $date_read
 * @property int|null $date_send
 * @property int|null $user_chat_id
 */
class ChatMessage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'date_add', 'date_read'], 'default', 'value' => null],
            [['chat_id', 'message'], 'required'],
            [['chat_id', 'author_id', 'date_add', 'date_read', 'date_send', 'user_chat_id'], 'integer'],
            [['message'], 'string', 'max' => 4100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'message' => 'Message',
            'author_id' => 'Author ID',
            'date_add' => 'Date Add',
            'date_read' => 'Date Read',
            'date_send' => 'Date Send',
        ];
    }

    public function getManagerToChat()
    {
        return $this->hasOne(ManagerToChat::className(), ['chat_id' => 'chat_id']);
    }
}
