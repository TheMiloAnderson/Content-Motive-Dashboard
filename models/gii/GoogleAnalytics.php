<?php

namespace app\models\gii;

use Yii;
use app\models\gii\GoogleAnalyticsProperties;

/**
 * This is the model class for table "ga_analytics".
 *
 * @property integer $id
 * @property integer $property_id
 * @property string $date_recorded
 * @property string $page
 * @property integer $pageviews
 * @property integer $visitors
 * @property string $avg_time
 * @property integer $entrances
 * @property string $bounce_rate
 * @property integer $click_through
 *
 * @property GaProperties $property
 */
class GoogleAnalytics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ga_analytics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id'], 'required'],
            [['property_id', 'pageviews', 'visitors', 'entrances', 'click_through'], 'integer'],
            [['date_recorded'], 'safe'],
            [['avg_time', 'bounce_rate'], 'number'],
            [['page'], 'string', 'max' => 120],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => GoogleAnalyticsProperties::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'property_id' => 'Property ID',
            'date_recorded' => 'Date Recorded',
            'page' => 'Page',
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
    public function getProperty()
    {
        return $this->hasOne(GoogleAnalyticsProperties::className(), ['id' => 'property_id']);
    }
}
