<?php

namespace app\models\gii;

/**
 * This is the ActiveQuery class for [[GoogleAnalyticsDetails]].
 *
 * @see GoogleAnalyticsDetails
 */
class GoogleAnalyticsDetailsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoogleAnalyticsDetails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoogleAnalyticsDetails|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
