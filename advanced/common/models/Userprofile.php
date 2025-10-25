<?php

namespace common\models;

/**
 * This is the model class for table "userprofile".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $password_hash
 * @property int $ativo
 * @property int $consulta_id
 * @property int $triagem_id
 */
class Userprofile extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userprofile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ativo'], 'default', 'value' => 1],
            [['id', 'nome', 'email', 'password_hash', 'consulta_id', 'triagem_id'], 'required'],
            [['id', 'ativo', 'consulta_id', 'triagem_id'], 'integer'],
            [['nome', 'email'], 'string', 'max' => 100],
            [['password_hash'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'ativo' => 'Ativo',
            'consulta_id' => 'Consulta ID',
            'triagem_id' => 'Triagem ID',
        ];
    }

}
