<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "dealers".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 *
 * @property DealerAccess[] $dealerAccesses
 * @property GaProperties[] $gaProperties
 */
class Dealers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dealers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealerAccesses()
    {
        return $this->hasMany(DealerAccess::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGaProperties()
    {
        return $this->hasMany(GoogleAnalyticsProperties::className(), ['dealer_id' => 'id']);
    }
}
