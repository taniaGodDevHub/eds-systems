<?php

use yii\db\Migration;

class m250811_133426_update_chat_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('chat_message', 'date_send', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('chat_message', 'date_send');
    }
}
