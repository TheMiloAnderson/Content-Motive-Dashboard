<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010711_Relations extends Migration
{

    public function init()
    {
       $this->db = 'db';
       parent::init();
    }

    public function safeUp()
    {
        $this->addForeignKey('fk_dealer_access_dealer_id',
            '{{%dealer_access}}','dealer_id',
            '{{%dealers}}','id',
            'CASCADE','CASCADE'
         );
        $this->addForeignKey('fk_dealer_access_user_id',
            '{{%dealer_access}}','user_id',
            '{{%users}}','id',
            'CASCADE','CASCADE'
         );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_dealer_access_dealer_id', '{{%dealer_access}}');
        $this->dropForeignKey('fk_dealer_access_user_id', '{{%dealer_access}}');
    }
}
