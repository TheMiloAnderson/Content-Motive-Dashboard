<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[GaAnalyticsData]].
 *
 * @see GaAnalyticsData
 */
class GaAnalyticsDataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GaAnalyticsData[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GaAnalyticsData|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
