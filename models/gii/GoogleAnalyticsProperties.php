<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "ga_properties".
 *
 * @property integer $id
 * @property integer $dealer_id
 * @property string $url
 * @property string $ga_view
 * @property string $start_date
 * @property string $type
 *
 * @property GaAnalytics[] $gaAnalytics
 * @property GaAnalyticsAggregates[] $gaAnalyticsAggregates
 * @property Dealers $dealer
 */
class GoogleAnalyticsProperties extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ga_properties';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dealer_id'], 'integer'],
            [['start_date'], 'required'],
            [['start_date'], 'safe'],
            [['url', 'type'], 'string', 'max' => 45],
            [['ga_view'], 'string', 'max' => 10],
            [['url'], 'unique'],
            [['dealer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dealers::className(), 'targetAttribute' => ['dealer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dealer_id' => 'Dealer ID',
            'url' => 'Url',
            'ga_view' => 'Ga View',
            'start_date' => 'Start Date',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGaAnalytics()
    {
        return $this->hasMany(GaAnalytics::className(), ['property_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGaAnalyticsAggregates()
    {
        return $this->hasMany(GaAnalyticsAggregates::className(), ['property_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealers::className(), ['id' => 'dealer_id']);
    }

    /**
     * @inheritdoc
     * @return GoogleAnalyticsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GoogleAnalyticsQuery(get_called_class());
    }
}
