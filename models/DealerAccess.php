<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dealer_access".
 *
 * @property integer $user_id
 * @property integer $dealer_id
 *
 * @property Dealers $dealer
 * @property Users $user
 */
class DealerAccess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dealer_access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'dealer_id'], 'integer'],
            [['dealer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dealers::className(), 'targetAttribute' => ['dealer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'dealer_id' => 'Dealer ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealers::className(), ['id' => 'dealer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return DealerAccessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DealerAccessQuery(get_called_class());
    }
}
