<?php

use yii\db\Migration;

class m250811_113219_update_manager_to_chat_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%manager_to_chat}}', 'status_id', $this->integer()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%manager_to_chat}}', 'status_id');
    }
}
