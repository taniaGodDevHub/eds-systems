<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tg_message".
 *
 * @property int $id
 * @property int $author_id
 * @property int $message_id
 * @property int $chat_id
 * @property int $text
 */
class TgMessage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tg_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'message_id', 'chat_id'], 'required'],
            [['author_id', 'message_id', 'chat_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'message_id' => 'Message ID',
            'chat_id' => 'Chat ID',
        ];
    }

}
