<?php

namespace common\models;
use common\models\UserProfile; // adiciona no topo se ainda não tiver
use Yii;

/**
 * Esta é a classe modelo para a tabela "triagem".
 *
 * @property int $id
 * @property string|null $motivoconsulta
 * @property string|null $queixaprincipal
 * @property string|null $descricaosintomas
 * @property string|null $iniciosintomas
 * @property int|null $intensidadedor
 * @property string|null $alergias
 * @property string|null $medicacao
 * @property string|null $datatriagem
 * @property int $userprofile_id
 * @property int|null $pulseira_id
 *
 * @property Pulseira $pulseira
 * @property UserProfile $userProfile
 */
class Triagem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'triagem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['queixaprincipal', 'descricaosintomas', 'alergias', 'medicacao'], 'string'],
            [['iniciosintomas', 'datatriagem'], 'safe'],
            [['intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [['userprofile_id'], 'required'],
            [['motivoconsulta'], 'string', 'max' => 255],
            [['intensidadedor'], 'integer', 'min' => 0, 'max' => 10],

            // Relações
            [['pulseira_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Pulseira::class, 'targetAttribute' => ['pulseira_id' => 'id']],
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
            'motivoconsulta' => 'Motivo da Consulta',
            'queixaprincipal' => 'Queixa Principal',
            'descricaosintomas' => 'Descrição dos Sintomas',
            'iniciosintomas' => 'Início dos Sintomas',
            'intensidadedor' => 'Intensidade da Dor (0-10)',
            'alergias' => 'Alergias Conhecidas',
            'medicacao' => 'Medicação Atual',
            'datatriagem' => 'Data da Triagem',
            'userprofile_id' => 'Perfil do Utilizador',
            'pulseira_id' => 'Pulseira Associada',
        ];
    }

    /**
     * 🔹 Relação com a pulseira criada nesta triagem
     */
    public function getPulseira()
    {
        return $this->hasOne(\common\models\Pulseira::class, ['id' => 'pulseira_id']);
    }

    /**
     * 🔹 Relação com o perfil do utilizador
     */
    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }
    public function getConsulta()
    {
        return $this->hasOne(\common\models\Consulta::class, ['triagem_id' => 'id']);
    }
}
