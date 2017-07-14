<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Dealers]].
 *
 * @see Dealers
 */
class DealersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Dealers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Dealers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
