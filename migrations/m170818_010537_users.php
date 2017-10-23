<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010537_users extends Migration
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
            'users',
            [
                'id'=> $this->primaryKey(11),
                'username'=> $this->string(45)->notNull(),
                'password'=> $this->string(60)->notNull(),
                'email'=> $this->string(45)->notNull(),
                'authKey'=> $this->string(45)->null()->defaultValue(null),
                'accessToken'=> $this->string(45)->null()->defaultValue(null),
                'admin'=> $this->smallInteger(1)->notNull()->defaultValue(0),
                'password_reset_token'=> $this->string(45)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->createIndex('name_UNIQUE','{{%users}}',['username'],true);
        $this->createIndex('email_UNIQUE','{{%users}}',['email'],true);

    }

    public function safeDown()
    {
        $this->dropIndex('name_UNIQUE', '{{%users}}');
        $this->dropIndex('email_UNIQUE', '{{%users}}');
        $this->dropTable('users');
    }
}
