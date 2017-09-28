<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010854_ga_analytics_details extends Migration
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
            'ga_analytics_details',
            [
                'property_id'=> $this->integer(4)->notNull(),
                'page'=> $this->string(120)->notNull(),
                'pageviews'=> $this->integer(10)->notNull(),
                'visitors'=> $this->integer(10)->notNull(),
                'entrances'=> $this->integer(10)->notNull(),
                'avg_time'=> $this->decimal(8, 2)->notNull(),
                'bounce_rate'=> $this->decimal(5, 4)->notNull(),
            ],$tableOptions
        );
        $this->addPrimaryKey('pk_on_ga_analytics_details','{{%ga_analytics_details}}',['property_id','page']);

    }

    public function safeDown()
    {
    $this->dropPrimaryKey('pk_on_ga_analytics_details','{{%ga_analytics_details}}');
        $this->dropTable('ga_analytics_details');
    }
}
