<?php

use yii\db\Migration;

class m171019_174611_add_analytics_clickthrough_column extends Migration
{
    public function safeUp()
    {
		$this->addColumn('ga_analytics', 'click_through', 'integer');
		$this->addColumn('ga_analytics_aggregates', 'click_through', 'integer');
		$this->addColumn('ga_analytics_details', 'click_through', 'integer');
		$this->alterColumn('ga_analytics_details', 'bounce_rate', 'decimal(5,2) NULL');
    }

    public function safeDown()
    {
        echo "m171019_174611_add_analytics_clickthrough_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171019_174611_add_analytics_clickthrough_column cannot be reverted.\n";

        return false;
    }
    */
}
