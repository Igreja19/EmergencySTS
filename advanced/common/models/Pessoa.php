<?php
namespace common\models;

use yii\db\ActiveRecord;

class Pessoa extends ActiveRecord
{
    public static function tableName()
    {
        return 'pessoa';
    }

    public function rules()
    {
        return [
            [['nome', 'nif', 'email'], 'required'],

            [['idade'], 'integer'],
            [['email'], 'email'],

            [['nome', 'morada'], 'string', 'max' => 80],

            [['nif'], 'string', 'length' => 9],
            [['nif'], 'unique', 'message' => 'Este NIF já existe na base de dados.'],
        ];
    }
}