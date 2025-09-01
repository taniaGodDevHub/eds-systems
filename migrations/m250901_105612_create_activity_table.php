<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%activity}}`.
 */
class m250901_105612_create_activity_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%activity}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'start_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->null(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%activity}}');
    }
}
