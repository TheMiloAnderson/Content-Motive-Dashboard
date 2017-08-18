<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010817_Relations extends Migration
{

    public function init()
    {
       $this->db = 'db';
       parent::init();
    }

    public function safeUp()
    {
        $this->addForeignKey('fk_ga_analytics_property_id',
            '{{%ga_analytics}}','property_id',
            '{{%ga_properties}}','id',
            'CASCADE','CASCADE'
         );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ga_analytics_property_id', '{{%ga_analytics}}');
    }
}
