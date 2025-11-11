<?php

namespace common\models;

use Yii;

/**
 * Esta é a classe modelo para a tabela "pulseira".
 *
 * @property int $id
 * @property string $codigo
 * @property string $prioridade
 * @property string|null $status
 * @property string $tempoentrada
 * @property int $userprofile_id
 *
 * @property UserProfile $userprofile
 * @property Triagem $triagem
 */
class Pulseira extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pulseira';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'prioridade', 'tempoentrada', 'userprofile_id'], 'required'],
            [['tempoentrada'], 'safe'],
            [['userprofile_id'], 'integer'],
            [['prioridade'], 'in', 'range' => ['Vermelha', 'Laranja', 'Amarela', 'Verde', 'Azul']],
            [['status'], 'in', 'range' => ['Em espera', 'Em atendimento', 'Atendido']],
            [['codigo'], 'string', 'max' => 10],
            [['codigo'], 'unique'],
            [['userprofile_id'], 'exist', 'skipOnError' => true,
                'targetClass' => UserProfile::class, 'targetAttribute' => ['userprofile_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código da Pulseira',
            'prioridade' => 'Prioridade',
            'status' => 'Estado',
            'tempoentrada' => 'Tempo de Entrada',
            'userprofile_id' => 'Utilizador',
        ];
    }

    /**
     * 🔹 Relação com o perfil do utilizador
     */
    public function getUserprofile()
    {
        return $this->hasOne(\common\models\UserProfile::class, ['id' => 'userprofile_id']);
    }

    /**
     * 🔹 Relação com a triagem (uma triagem cria uma pulseira)
     */
    public function getTriagem()
    {
        return $this->hasOne(\common\models\Triagem::class, ['pulseira_id' => 'id']);
    }

    /**
     * 🔹 Texto formatado da prioridade com ícone
     */
    public function getPrioridadeComCor()
    {
        $cores = [
            'Vermelha' => '🔴 Vermelha - Emergente',
            'Laranja'  => '🟠 Laranja - Muito Urgente',
            'Amarela'  => '🟡 Amarela - Urgente',
            'Verde'    => '🟢 Verde - Pouco Urgente',
            'Azul'     => '🔵 Azul - Não Urgente',
        ];
        return $cores[$this->prioridade] ?? $this->prioridade;
    }
}
