<?php

use yii\db\Schema;
use yii\db\Migration;

class m171205_004705_usersDataInsert extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $this->batchInsert('{{%users}}',
                           ["id", "username", "password", "email", "authKey", "accessToken", "admin"],
                            [
    [
        'id' => '1',
        'username' => 'milo',
        'password' => '$2y$13$OS0qegOQZRiMcbZFBpa1.uO5ad.OFALmqpFpnv4SoWQUxc3KkTVD2',
        'email' => 'themiloanderson@gmail.com',
        'authKey' => '',
        'accessToken' => '',
        'admin' => '1',
    ],
]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%users}} CASCADE');
    }
}
