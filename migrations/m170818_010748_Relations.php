<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010748_Relations extends Migration
{

    public function init()
    {
       $this->db = 'db';
       parent::init();
    }

    public function safeUp()
    {
        $this->addForeignKey('fk_ga_properties_dealer_id',
            '{{%ga_properties}}','dealer_id',
            '{{%dealers}}','id',
            'CASCADE','CASCADE'
         );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ga_properties_dealer_id', '{{%ga_properties}}');
    }
}
