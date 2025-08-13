<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m250810_132429_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer()->notNull(),
            'f' => $this->string()->notNull(),
            'i' => $this->string()->notNull(),
            'o' => $this->string()->notNull(),
            'date_add' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client}}');
    }
}
