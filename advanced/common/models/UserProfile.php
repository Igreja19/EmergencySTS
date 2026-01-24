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
 * @property int $user_id
 *
 * @property Notificacao[] $notificacaos
 * @property Triagem[] $triagems
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
{
    public $role;
    public $password;

    /** Estados do perfil */
    const ATIVO = 1;
    const DESATIVO = 0;

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
            [['nome', 'email', 'nif', 'sns', 'datanascimento', 'genero', 'telefone', 'morada', 'user_id'], 'required'],

            [['datanascimento'], 'safe'],
            [['user_id'], 'integer'],
            ['email', 'email'],

            ['estado', 'boolean'],
            ['estado', 'default', 'value' => self::ATIVO],

            [['nome', 'email'], 'string', 'max' => 100],
            [['morada'], 'string', 'max' => 255],
            [['genero'], 'string', 'max' => 1],
            [['telefone'], 'string', 'max' => 30, 'min' => 9],
            [['sns', 'nif'], 'string', 'max' => 9, 'min' => 9],
            ['password', 'string', 'min' => 6],


            [['email'], 'unique', 'filter' => $this->isNewRecord ? [] : ['!=', 'id', $this->id]],
            [['nif'], 'unique', 'filter' => $this->isNewRecord ? [] : ['!=', 'id', $this->id], 'message' => 'Este NIF já está registado.'],
            [['sns'], 'unique', 'filter' => $this->isNewRecord ? [] : ['!=', 'id', $this->id], 'message' => 'Este número SNS já está registado.'],
            ['nif', 'match', 'pattern' => '/^[0-9]+$/', 'message' => 'O NIF só pode conter números.'],
            ['sns', 'match', 'pattern' => '/^[0-9]+$/', 'message' => 'O SNS só pode conter números.'],
            ['telefone', 'match', 'pattern' => '/^[0-9]+$/', 'message' => 'O telefone só pode conter números.'],

            [['role'], 'safe'],

            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            ['genero', 'in', 'range' => ['M', 'F', 'O']],
            ['datanascimento', 'date', 'format' => 'php:Y-m-d'],
            ['datanascimento', 'compare', 'compareValue' => date('Y-m-d'), 'operator' => '<=', 'type' => 'date'],
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
            'sns' => 'SNS',
            'datanascimento' => 'Data de Nascimento',
            'genero' => 'Género',
            'telefone' => 'Telefone',
            'role' => 'Função / Role',
            'user_id' => 'User ID',
            'estado' => 'Ativo',
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

    public function getPulseiras()
    {
        return $this->hasMany(\common\models\Pulseira::class, ['userprofile_id' => 'id']);
    }

    public function desativar(): bool
    {
        $this->estado = self::DESATIVO;
        return $this->save(false);
    }

    public function ativar(): bool
    {
        $this->estado = self::ATIVO;
        return $this->save(false);
    }

    public function isAtivo(): bool
    {
        return (int)$this->estado === self::ATIVO;
    }

    public function getEstadoLabel(): string
    {
        return $this->isAtivo() ? 'Ativo' : 'Desativado';
    }

    public function getEstadoBadge(): string
    {
        return $this->isAtivo()
            ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Ativo</span>'
            : '<span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Desativado</span>';
    }
}
