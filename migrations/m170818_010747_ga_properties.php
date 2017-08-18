<?php

use yii\db\Schema;
use yii\db\Migration;

class m170818_010747_ga_properties extends Migration
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
            'ga_properties',
            [
                'id'=> $this->primaryKey(11),
                'dealer_id'=> $this->integer(11)->null()->defaultValue(null),
                'url'=> $this->string(45)->null()->defaultValue(null),
                'ga_view'=> $this->string(10)->null()->defaultValue(null),
                'start_date'=> $this->date()->notNull(),
                'type'=> $this->string(45)->null()->defaultValue(null),
            ],$tableOptions
        );
        $this->createIndex('url_UNIQUE','{{%ga_properties}}',['url'],true);
        $this->createIndex('dealer_id_idx','{{%ga_properties}}',['dealer_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('url_UNIQUE', '{{%ga_properties}}');
        $this->dropIndex('dealer_id_idx', '{{%ga_properties}}');
        $this->dropTable('ga_properties');
    }
}
