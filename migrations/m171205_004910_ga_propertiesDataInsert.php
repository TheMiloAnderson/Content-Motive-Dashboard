<?php

use yii\db\Schema;
use yii\db\Migration;

class m171205_004910_ga_propertiesDataInsert extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $this->batchInsert('{{%ga_properties}}',
                           ["id", "dealer_id", "url", "ga_view", "start_date", "type"],
                            [
    [
        'id' => '46',
        'dealer_id' => '8',
        'url' => 'www.dealeracars.net',
        'ga_view' => '00000000',
        'start_date' => '2014-09-01',
        'type' => 'Content',
    ],
    [
        'id' => '47',
        'dealer_id' => '8',
        'url' => 'www.dealeratrucks.com',
        'ga_view' => '00000000',
        'start_date' => '2017-01-01',
        'type' => 'Content',
    ],
    [
        'id' => '55',
        'dealer_id' => '16',
        'url' => 'www.dealerbcarsandtrucks.com',
        'ga_view' => '00000000',
        'start_date' => '2016-01-01',
        'type' => 'Content',
    ],
]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%ga_properties}} CASCADE');
    }
}
