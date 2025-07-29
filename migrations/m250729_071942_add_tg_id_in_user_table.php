<?php

use yii\db\Migration;

class m250729_071942_add_tg_id_in_user_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'tg_id', $this->bigInteger()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'tg_id');
    }
}
