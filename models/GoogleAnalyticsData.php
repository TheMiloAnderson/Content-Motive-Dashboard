<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ga_analytics".
 *
 * @property integer $id
 * @property integer $property_id
 * @property string $date_recorded
 * @property string $page
 * @property integer $pageviews
 * @property integer $unique_pageviews
 * @property integer $avg_time
 * @property integer $entrances
 * @property string $bounce_rate
 *
 * @property GaProperties $property
 */
class GoogleAnalyticsData extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ga_analytics';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['property_id'], 'required'],
            [['property_id', 'pageviews', 'visitors', 'entrances','click_through'], 'integer'],
            [['date_recorded'], 'safe'],
            [['bounce_rate', 'avg_time'], 'number'],
            [['page'], 'string', 'max' => 120],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => GaProperties::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'property_id' => 'Property ID',
            'date_recorded' => 'Date Recorded',
            'page' => 'Content Strategy',
            'pageviews' => 'Pageviews',
            'visitors' => 'Visitors',
            'avg_time' => 'Avg Time',
            'entrances' => 'Entrances',
            'bounce_rate' => 'Bounce Rate',
            'click_through' => 'Click Through',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty() {
        return $this->hasOne(GaProperties::className(), ['id' => 'property_id']);
    }

    /**
     * @inheritdoc
     * @return GaAnalyticsDataQuery the active query used by this AR class.
     */
    public static function find() {
        return new GaAnalyticsDataQuery(get_called_class());
    }

}
