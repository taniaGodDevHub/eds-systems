<?php

use yii\db\Migration;

class m250814_183850_update_chat_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('chat_message', 'user_chat_id', $this->bigInteger()->null());
        $this->alterColumn('chat_message', 'chat_id', $this->bigInteger()->null());
        $this->alterColumn('manager_to_chat', 'chat_id', $this->bigInteger()->null());
        $this->alterColumn('manager_to_chat', 'client_id', $this->bigInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
