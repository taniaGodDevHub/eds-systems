<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%manager_to_chat}}`.
 */
class m250729_133352_create_manager_to_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%manager_to_chat}}', [
            'id' => $this->primaryKey(),
            'manager_id' => $this->integer()->notNull(),
            'chat_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%manager_to_chat}}');
    }
}
