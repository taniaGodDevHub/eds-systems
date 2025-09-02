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

    /**
     * Расчет среднего времени ответа менеджера
     * @param integer[] $managerIds - Массив ID менеджеров
     * @param integer $periodStart - Начальное время периода (timestamp)
     * @param integer $periodEnd - Конечное время периода (timestamp)
     * @return float|null - Среднее время ответа в секундах или null, если данных недостаточно
     */
    public static function calculateAverageResponseTime($managerIds, $periodStart, $periodEnd)
    {
        // Подзапрос для выбора первого сообщения клиента и первого ответа менеджера
        $subQuery = self::find()
            ->innerJoinWith(['managerToChat'], true, 'INNER JOIN') // Соединение с таблицей manager_to_chat
            ->select([
                'chat_message.chat_id',
                'MIN(chat_message.date_add) AS first_client_message_time',
                'MIN(IF(chat_message.author_id IN (' . implode(',', $managerIds) . '), chat_message.date_add, NULL)) AS first_manager_response_time'
            ])
            ->groupBy('chat_message.chat_id')
            ->having('first_client_message_time IS NOT NULL AND first_manager_response_time IS NOT NULL')
            ->andFilterWhere(['>=', 'chat_message.date_add', $periodStart]) // сравнение по timestamp
            ->andFilterWhere(['<=', 'chat_message.date_add', $periodEnd]); // comparison by timestamp

        // Главный запрос: расчет среднего времени ответа
        $query = self::find()
            ->select([
                'AVG(first_manager_response_time - first_client_message_time) AS average_response_time'
            ])
            ->from(['chat_messages_with_responses' => $subQuery]);

        $result = $query->one();
        return isset($result['average_response_time']) ? (float)$result['average_response_time'] : '-';
    }
}
