<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\gii\GoogleAnalyticsProperties;
use app\models\gii\DealerAccess;
/**
 * This is the model class for table "dealers".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property DealerAccess[] $dealerAccesses
 */
class DealersWithProperties extends \app\models\gii\Dealers {
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealerAccesses() {
        return $this->hasMany(DealerAccess::className(), ['dealer_id' => 'id']);
    }
    public function getGaProperties() {
        return $this->hasMany(GoogleAnalyticsProperties::className(), ['dealer_id' => 'id']);
    }
    public function getContentProperties() {
        $links = ['dealer_id' => 'id'];
        return $this->hasMany(GoogleAnalyticsProperties::className(), $links)
            ->onCondition(['type' => 'Content']);
    }
    public function getBlogProperties() {
        $links = ['dealer_id' => 'id'];
        return $this->hasMany(GoogleAnalyticsProperties::className(), $links)
            ->onCondition(['type' => 'Blogs']);
    }
    public function getReviewProperties() {
        $links = ['dealer_id' => 'id'];
        return $this->hasMany(GoogleAnalyticsProperties::className(), $links)
            ->onCondition(['type' => 'Reviews']);
    }
    public function getMicroProperties() {
        $links = ['dealer_id' => 'id'];
        return $this->hasMany(GoogleAnalyticsProperties::className(), $links)
            ->onCondition(['type' => 'Microsites']);
    }
    public static function getAvailableDealers() {
        $dealers = self::find()->orderBy('name')->asArray()->all();
        $items = ArrayHelper::map($dealers, 'id', 'name');
        return $items;
    }
}