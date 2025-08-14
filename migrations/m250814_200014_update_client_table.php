<?php

use yii\db\Migration;

class m250814_200014_update_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('client', 'chat_id', $this->bigInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250814_200014_update_client_table cannot be reverted.\n";

        return false;
    }

}
