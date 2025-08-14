<?php

namespace app\controllers;


use app\models\ChatMessage;
use app\models\ManagerToChat;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\T;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class ChatController extends AccessController
{

    public function actionIndex()
    {
        $chats = ManagerToChat::find()
            ->where(['manager_id' => Yii::$app->user->identity->id])
            ->andWhere(['status_id' => 1])
            ->all();

        $chatsWithClientForm = [];
        foreach ($chats as $chat) {
            $chatsWithClientForm[] = [
                'chat' => $chat,
                'client_form' => $chat->client,
            ];
        }
        return $this->render('index',
            [
                'chats' => $chatsWithClientForm,
            ]);
    }

    /**
     * Возвращает список сообщений в чате
     * @param int $chat_id
     * @return Response
     * @throws MethodNotAllowedHttpException
     */
    public function actionGetMessages(int $chat_id): Response
    {
        if (!$this->request->isAjax) {
            throw new MethodNotAllowedHttpException("Only ajax requests are allowed");
        }

        return $this->asJson(ChatMessage::find()
            ->where(['chat_id' => $chat_id])
            ->orderBy(['date_add' => SORT_ASC])
            ->all());
    }

    /**
     * Помечает все сообщения чата прочитанными
     * @param int $chat_id
     * @return Response
     * @throws MethodNotAllowedHttpException
     */
    public function actionSetRead(int $chat_id): Response
    {
        if (!$this->request->isAjax) {
            throw new MethodNotAllowedHttpException("Only ajax requests are allowed");
        }


        ChatMessage::updateAll(['date_read' => time()], ['chat_id' => $chat_id, 'date_read' => null]);
        return $this->asJson(true);
    }

    /**
     * Меняет статус чата на "закрыт" - 0
     * @param int $id
     * @return Response
     * @throws MethodNotAllowedHttpException
     * @throws ServerErrorHttpException
     * @throws Exception
     */
    public function actionClose(int $id): Response
    {
        if (!$this->request->isAjax) {
            throw new MethodNotAllowedHttpException("Only ajax requests are allowed");
        }

        $mtc = ManagerToChat::findOne(['chat_id' => $id]);
        $mtc->status_id = 0;

        if (!$mtc->save()) {
            throw new ServerErrorHttpException("Failed to close message chat");
        }

        return $this->asJson(true);
    }

    /**
     * Сохраняет сообщение в БД
     * @return Response
     * @throws Exception
     * @throws MethodNotAllowedHttpException
     * @throws ServerErrorHttpException
     */
    public function actionSendMessage(): Response
    {
        if (!$this->request->isPost) {
            throw new MethodNotAllowedHttpException("Only post requests are allowed");
        }

        $msg = new ChatMessage();
        $msg->chat_id = $this->request->post('chat_id');
        $msg->message = $this->request->post('message');
        $msg->author_id = Yii::$app->user->identity->id;
        $msg->date_add = time();
        $msg->date_send = time();
        if (!$msg->save()) {
            throw new ServerErrorHttpException("Failed to save message" . print_r($msg->getErrors(), true));
        }

        $telegram = Yii::$app->telegram;
        $telegram->sendMessage([
            'chat_id' => (int)$msg->chat_id,
            'text' => $this->request->post('message')
        ]);

        return $this->asJson(true);
    }
}
