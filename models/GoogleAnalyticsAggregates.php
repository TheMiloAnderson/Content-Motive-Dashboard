<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ga_analytics_aggregates".
 *
 * @property integer $property_id
 * @property string $date_recorded
 * @property integer $pageviews
 * @property integer $visitors
 * @property integer $entrances
 * @property string $avg_time
 * @property string $bounce_rate
 *
 * @property GaProperties $property
 */
class GoogleAnalyticsAggregates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ga_analytics_aggregates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id', 'date_recorded'], 'required'],
            [['property_id', 'pageviews', 'visitors', 'entrances'], 'integer'],
            [['date_recorded'], 'safe'],
            [['avg_time', 'bounce_rate'], 'number'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => GaProperties::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'property_id' => 'Property ID',
            'date_recorded' => 'Date Recorded',
            'pageviews' => 'Pageviews',
            'visitors' => 'Visitors',
            'entrances' => 'Entrances',
            'avg_time' => 'Avg Time',
            'bounce_rate' => 'Bounce Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(GaProperties::className(), ['id' => 'property_id']);
    }

    /**
     * @inheritdoc
     * @return GaAnalyticsAggregatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GaAnalyticsAggregatesQuery(get_called_class());
    }
}
