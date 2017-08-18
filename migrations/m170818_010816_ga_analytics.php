<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010816_ga_analytics extends Migration
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
            'ga_analytics',
            [
                'id'=> $this->primaryKey(11),
                'property_id'=> $this->integer(11)->notNull(),
                'date_recorded'=> $this->date()->null()->defaultValue(null),
                'page'=> $this->string(120)->null()->defaultValue(null),
                'pageviews'=> $this->integer(11)->null()->defaultValue(null),
                'visitors'=> $this->integer(11)->null()->defaultValue(null),
                'avg_time'=> $this->decimal(8, 2)->null()->defaultValue(null),
                'entrances'=> $this->integer(11)->null()->defaultValue(null),
                'bounce_rate'=> $this->decimal(5, 2)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->createIndex('property_id_idx','{{%ga_analytics}}',['property_id'],false);
        $this->createIndex('property_id','{{%ga_analytics}}',['property_id','date_recorded'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('property_id_idx', '{{%ga_analytics}}');
        $this->dropIndex('property_id', '{{%ga_analytics}}');
        $this->dropTable('ga_analytics');
    }
}
