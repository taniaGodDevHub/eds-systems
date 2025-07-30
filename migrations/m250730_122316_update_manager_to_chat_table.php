<?php

use yii\db\Migration;

class m250730_122316_update_manager_to_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%manager_to_chat}}', 'client_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%manager_to_chat}}', 'client_id');
    }
}
