<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010835_ga_analytics_aggregates extends Migration
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
            'ga_analytics_aggregates',
            [
                'property_id'=> $this->integer(4)->notNull(),
                'date_recorded'=> $this->date()->notNull(),
                'pageviews'=> $this->integer(10)->null()->defaultValue(null),
                'visitors'=> $this->integer(10)->null()->defaultValue(null),
                'entrances'=> $this->integer(10)->null()->defaultValue(null),
                'avg_time'=> $this->decimal(8, 2)->null()->defaultValue(null),
                'bounce_rate'=> $this->decimal(5, 2)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->addPrimaryKey('pk_on_ga_analytics_aggregates','{{%ga_analytics_aggregates}}',['property_id','date_recorded']);

    }

    public function safeDown()
    {
    $this->dropPrimaryKey('pk_on_ga_analytics_aggregates','{{%ga_analytics_aggregates}}');
        $this->dropTable('ga_analytics_aggregates');
    }
}
