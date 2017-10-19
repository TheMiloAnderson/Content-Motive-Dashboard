<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\models\gii\DealerAccess;
use app\models\DealersWithProperties;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $authKey
 * @property string $accessToken
 * @property string $role
 * @property string $password_reset_token
 * 
 * @property DealerAccess[] $dealerAccesses
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username', 'email', 'admin'], 'required'],
            [['username', 'email', 'authKey', 'accessToken'], 'string', 'max' => 45],
            [['password'], 'string', 'max' => 60],
            [['username', 'email', 'password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'authKey' => 'Auth Key',
            'accessToken' => 'Access Token',
            'admin' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealerAccesses() {
        return $this->hasMany(DealerAccess::className(), ['user_id' => 'id']);
    }
    
    public function getDealers() {
        return $this->hasMany(DealersWithProperties::className(), ['id' => 'dealer_id'])
            ->via('dealerAccesses')
            ->orderBy('name');
    }

    /**
     * @inheritdoc
     * @return UsersQuery the active query used by this AR class.
     */
//    public static function find() {
//        return new UsersQuery(get_called_class());
//    }
    public static function findIdentity($id) {
        $user = self::find()
            ->where(["id" => $id])
            ->one();
        if (!count($user)) {
            return null;
        }
        return new static($user);
    }
    public static function findIdentityByAccessToken($token, $type = null) {
        $user = self::find()
            ->where(["accessToken" => $token])
            ->one();
        if (!count($user)) {
            return null;
        }
        return new static($user);
    }
    public static function findByUsername($username) {
        $user = self::find()
            ->where(["username" => $username])
            ->one();
        if (!count($user)) {
            return null;
        }
        return new static($user);
    }
    public function getId() {
        return $this->id;
    }
    public function getAuthKey() {
        return $this->authKey;
    }
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public function isAdmin() {
        return $this->admin === 1;
    }
    public function setPassword($password) {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    public function unsetPassword() {
        $this->password = '';
    }
    
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }
    
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            //'status' => self::STATUS_ACTIVE,
        ]);
    }
}