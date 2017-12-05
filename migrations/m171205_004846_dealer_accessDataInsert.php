<?php

use yii\db\Schema;
use yii\db\Migration;

class m171205_004846_dealer_accessDataInsert extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $this->batchInsert('{{%dealer_access}}',
                           ["user_id", "dealer_id"],
                            [
    [
        'user_id' => '1',
        'dealer_id' => '8',
    ],
    [
        'user_id' => '1',
        'dealer_id' => '16',
    ],
]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%dealer_access}} CASCADE');
    }
}
