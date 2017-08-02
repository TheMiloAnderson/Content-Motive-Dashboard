<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

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
            [['username', 'email'], 'required'],
            [['username', 'password', 'email', 'authKey', 'accessToken'], 'string', 'max' => 45],
            [['role'], 'string', 'max' => 10],
            [['username'], 'unique'],
            [['email'], 'unique'],
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
            'role' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealerAccesses() {
        return $this->hasMany(DealerAccess::className(), ['user_id' => 'id']);
    }
    
    public function getDealers() {
        return $this->hasMany(Dealers::className(), ['id' => 'dealer_id'])
            ->via('dealerAccesses')
            ->orderBy('name');
    }

    /**
     * @inheritdoc
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find() {
        return new UsersQuery(get_called_class());
    }
	
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
        return $this->password === $password;
    }
}