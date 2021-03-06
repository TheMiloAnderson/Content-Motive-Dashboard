<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "ga_analytics_details".
 *
 * @property integer $property_id
 * @property string $page
 * @property integer $pageviews
 * @property integer $visitors
 * @property integer $entrances
 * @property string $avg_time
 * @property string $bounce_rate
 * @property integer $click_through
 */
class GoogleAnalyticsDetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ga_analytics_details';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_id', 'page', 'pageviews', 'visitors', 'entrances', 'avg_time', 'bounce_rate'], 'required'],
            [['property_id', 'pageviews', 'visitors', 'entrances', 'click_through'], 'integer'],
            [['avg_time', 'bounce_rate'], 'number'],
            [['page'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'property_id' => 'Property ID',
            'page' => 'Page',
            'pageviews' => 'Pageviews',
            'visitors' => 'Visitors',
            'entrances' => 'Entrances',
            'avg_time' => 'Avg Time',
            'bounce_rate' => 'Bounce Rate',
            'click_through' => 'Click Through',
        ];
    }

    /**
     * @inheritdoc
     * @return GoogleAnalyticsDetailsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GoogleAnalyticsDetailsQuery(get_called_class());
    }
}
