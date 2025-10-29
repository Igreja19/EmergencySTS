<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "userprofile".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string|null $morada
 * @property string $nif
 * @property string $sns
 * @property string $datanascimento
 * @property string $genero
 * @property string $telefone
 * @property int $consulta_id
 * @property int $triagem_id
 * @property int $user_id
 *
 * @property Notificacao[] $notificacaos
 * @property Triagem[] $triagems
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
            [['nome', 'email', 'nif', 'sns', 'datanascimento', 'genero', 'telefone', 'consulta_id', 'triagem_id', 'user_id'], 'required'],
            [['datanascimento'], 'safe'],
            [['consulta_id', 'triagem_id', 'user_id'], 'integer'],
            [['nome', 'email'], 'string', 'max' => 100],
            [['morada'], 'string', 'max' => 255],
            [['nif', 'sns'], 'string', 'max' => 9],
            [['genero'], 'string', 'max' => 1],
            [['telefone'], 'string', 'max' => 30],
            [['email'], 'unique'],
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
            'morada' => 'Morada',
            'nif' => 'Nif',
            'sns' => 'Sns',
            'datanascimento' => 'Datanascimento',
            'genero' => 'Genero',
            'telefone' => 'Telefone',
            'consulta_id' => 'Consulta ID',
            'triagem_id' => 'Triagem ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[Notificacaos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificacaos()
    {
        return $this->hasMany(Notificacao::class, ['userprofile_id' => 'id']);
    }

    /**
     * Gets query for [[Triagems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTriagems()
    {
        return $this->hasMany(Triagem::class, ['userprofile_id' => 'id']);
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
