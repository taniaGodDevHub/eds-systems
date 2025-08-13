<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat_message}}`.
 */
class m250810_140029_create_chat_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat_message}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'message' => $this->string(4100)->notNull(),
            'author_id' => $this->integer()->null(),
            'date_add' => $this->integer()->null(),
            'date_read' => $this->integer()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%chat_message}}');
    }
}
