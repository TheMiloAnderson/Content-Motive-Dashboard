<?php

namespace app\models\gii;

/**
 * This is the ActiveQuery class for [[GoogleAnalyticsAggregates]].
 *
 * @see GoogleAnalyticsAggregates
 */
class GoogleAnalyticsAggregateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoogleAnalyticsAggregates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoogleAnalyticsAggregates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
