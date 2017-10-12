<?php

namespace app\models;

use Yii;
use app\models\Users;
use app\models\gii\Dealers;
use app\models\gii\DealerAccess;
use app\models\gii\GoogleAnalyticsProperties;
use yii\helpers\ArrayHelper;

class UsersWithDealers extends Users {

    public $dealer_ids = [];

    public function rules() {
        return ArrayHelper::merge(
            parent::rules(), [['dealer_ids', 'each', 'rule' => [
                'exist', 'targetClass' => Dealers::className(), 'targetAttribute' => 'id'
            ]]]
        );
    }
    public function attributeLabels() {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'dealer_ids' => 'Dealers',
        ]);
    }
    public function loadDealers() {
        $this->dealer_ids = [];
        if (!empty($this->id)) {
            $rows = DealerAccess::find()
                ->select(['dealer_id'])
                ->where(['user_id' => $this->id])
                ->asArray()
                ->all();
            foreach ($rows as $row) {
                $this->dealer_ids[] = $row['dealer_id'];
            }
        }
    }
    public function saveDealers() {
        DealerAccess::deleteAll("user_id = $this->id");
        $this->dealer_ids = Yii::$app->request->post('UsersWithDealers')['dealers'];
        if (is_array($this->dealer_ids)) {
            foreach ($this->dealer_ids as $id) {
                $dc = new DealerAccess();
                $dc->user_id = $this->id;
                $dc->dealer_id = $id;
                $dc->save();
            }
        }
    }
    public static function userHasContentType($type) {
        $userId = Yii::$app->user->identity->id;
        $props = GoogleAnalyticsProperties::find()
            ->joinWith('dealer.dealerAccesses.user u', false)
            ->where(['u.id' => $userId])
            ->asArray()
            ->all();
        $types = ArrayHelper::getColumn($props, 'type');
        return in_array($type, $types);
    }
}
