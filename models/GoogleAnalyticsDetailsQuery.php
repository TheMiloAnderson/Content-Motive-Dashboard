<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[GoogleAnalyticsDetails]].
 *
 * @see GaAnalyticsDetails
 */
class GoogleAnalyticsDetailsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GaAnalyticsDetails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GaAnalyticsDetails|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
