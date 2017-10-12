<?php

namespace app\gii\models;

/**
 * This is the ActiveQuery class for [[GaProperties]].
 *
 * @see GaProperties
 */
class GoogleAnalyticsPropertiesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/
    /**
     * @inheritdoc
     * @return GaProperties[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
    /**
     * @inheritdoc
     * @return GaProperties|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}