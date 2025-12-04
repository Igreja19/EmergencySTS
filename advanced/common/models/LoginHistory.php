<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class LoginHistory extends ActiveRecord
{
    public static function tableName()
    {
        return 'login_history';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}