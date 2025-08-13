<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property int $chat_id
 * @property string $f
 * @property string $i
 * @property string $o
 * @property int|null $date_add
 */
class Client extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_add'], 'default', 'value' => null],
            [['chat_id', 'f', 'i', 'o'], 'required'],
            [['chat_id', 'date_add'], 'integer'],
            [['f', 'i', 'o'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'ID чата в ТГ',
            'f' => 'Фамилия',
            'i' => 'Имя',
            'o' => 'Отчество',
            'date_add' => 'Добавлен',
        ];
    }


}
