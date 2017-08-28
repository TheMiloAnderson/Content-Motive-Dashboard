<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010710_dealer_access extends Migration
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
            'dealer_access',
            [
                'user_id'=> $this->integer(11)->null()->defaultValue(null),
                'dealer_id'=> $this->integer(11)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->createIndex('dealer_id_idx','{{%dealer_access}}',['dealer_id'],false);
        $this->createIndex('user_id_idx','{{%dealer_access}}',['user_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('dealer_id_idx', '{{%dealer_access}}');
        $this->dropIndex('user_id_idx', '{{%dealer_access}}');
        $this->dropTable('dealer_access');
    }
}
