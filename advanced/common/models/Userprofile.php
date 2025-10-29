<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "userprofile".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $nif
 * @property string $sns
 * @property string $datanascimento
 * @property string $genero
 * @property string $telefone
 * @property string $password_hash
 * @property int $ativo
 * @property int $consulta_id
 * @property int $triagem_id
 * @property int $user_id
 *
 * @property User $user
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
            [['id', 'nome', 'email', 'nif', 'sns', 'datanascimento', 'genero', 'telefone', 'password_hash', 'consulta_id', 'triagem_id', 'user_id'], 'required'],
            [['id', 'ativo', 'consulta_id', 'triagem_id', 'user_id'], 'integer'],
            [['datanascimento'], 'safe'],
            [['nome', 'email'], 'string', 'max' => 100],
            [['nif', 'sns'], 'string', 'max' => 9],
            [['genero'], 'string', 'max' => 1],
            [['telefone'], 'string', 'max' => 30],
            [['password_hash'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'nif' => 'Nif',
            'sns' => 'Sns',
            'datanascimento' => 'Datanascimento',
            'genero' => 'Genero',
            'telefone' => 'Telefone',
            'password_hash' => 'Password Hash',
            'ativo' => 'Ativo',
            'consulta_id' => 'Consulta ID',
            'triagem_id' => 'Triagem ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
