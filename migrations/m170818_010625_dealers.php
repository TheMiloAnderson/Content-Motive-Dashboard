<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010625_dealers extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            'dealers',
            [
                'id'=> $this->primaryKey(11),
                'name'=> $this->string(45)->notNull(),
                'code'=> $this->string(10)->null()->defaultValue(null),
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('dealers');
    }
}
