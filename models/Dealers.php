<?php

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "dealers".
 *
 * @property integer $id
 * @property string $name
 *
 * @property DealerAccess[] $dealerAccesses
 */
class Dealers extends \yii\db\ActiveRecord {
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'dealers';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Dealer Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealerAccesses() {
        return $this->hasMany(DealerAccess::className(), ['dealer_id' => 'id']);
    }
    
    public function getGaProperties() {
        return $this->hasMany(GaProperties::className(), ['dealer_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return DealersQuery the active query used by this AR class.
     */
    public static function find() {
        return new DealersQuery(get_called_class());
    }
	
    public static function getAvailableDealers() {
        $dealers = self::find()->orderBy('name')->asArray()->all();
        $items = ArrayHelper::map($dealers, 'id', 'name');
        return $items;
    }
}
