<?php

use yii\db\Schema;
use yii\db\Migration;

class m171205_004813_dealersDataInsert extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $this->batchInsert('{{%dealers}}',
                           ["id", "name", "code"],
                            [
    [
        'id' => '8',
        'name' => 'Dealership A',
        'code' => 'DLRA',
    ],
    [
        'id' => '16',
        'name' => 'Dealership B',
        'code' => 'DLRB',
    ],
    [
        'id' => '23',
        'name' => 'Dealership C',
        'code' => 'DLRC',
    ],
    [
        'id' => '24',
        'name' => 'Dealership D',
        'code' => 'DLRD',
    ],
]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%dealers}} CASCADE');
    }
}
