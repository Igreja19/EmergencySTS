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
 * @property int|null $consulta_id
 * @property int|null $triagem_id
 * @property int|null $user_id
 *
 * @property Notificacao[] $notificacaos
 * @property Triagem[] $triagems
 * @property User $user
 */
class Userprofile extends \yii\db\ActiveRecord
{
    public $role;

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
            // 🔹 Campos obrigatórios básicos
            [['nome', 'email', 'nif', 'sns', 'datanascimento', 'genero', 'telefone'], 'required'],

            // 🔹 Campos opcionais ou automáticos
            [['datanascimento'], 'safe'],
            [['consulta_id', 'triagem_id', 'user_id'], 'integer'],

            // 🔹 Limites de tamanho e formato
            [['nome', 'email'], 'string', 'max' => 100],
            [['morada'], 'string', 'max' => 255],
            [['nif', 'sns'], 'string', 'max' => 9],
            [['genero'], 'string', 'max' => 1],
            [['telefone'], 'string', 'max' => 30],

            // 🔹 Validações adicionais
            [['email'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

            // 🔹 Campo virtual "role" (não existe na BD, mas vem do form)
            [['role'], 'safe'],
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
            'nif' => 'NIF',
            'sns' => 'Número SNS',
            'datanascimento' => 'Data de Nascimento',
            'genero' => 'Género',
            'telefone' => 'Telefone',
            'consulta_id' => 'Consulta ID',
            'triagem_id' => 'Triagem ID',
            'user_id' => 'Utilizador',
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
