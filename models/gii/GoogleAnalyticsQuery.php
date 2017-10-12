<?php

namespace app\models\gii;

/**
 * This is the ActiveQuery class for [[GoogleAnalyticsProperties]].
 *
 * @see GoogleAnalyticsProperties
 */
class GoogleAnalyticsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoogleAnalyticsProperties[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoogleAnalyticsProperties|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
