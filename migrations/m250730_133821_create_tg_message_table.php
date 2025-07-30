<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tg_message}}`.
 */
class m250730_133821_create_tg_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tg_message}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'message_id' => $this->integer()->notNull(),
            'chat_id' => $this->integer()->notNull(),
            'text' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tg_message}}');
    }
}
