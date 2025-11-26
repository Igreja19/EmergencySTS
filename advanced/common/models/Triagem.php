<?php

namespace common\models;

use Yii;
use common\models\UserProfile;
use common\models\Pulseira; // <--- ISTO ESTAVA A FALTAR E CAUSAVA O ERRO 500!

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
    public $prioridade_pulseira;

    public static function tableName()
    {
        return 'triagem';
    }

    public function rules()
    {
        return [
            [['queixaprincipal', 'descricaosintomas', 'alergias', 'medicacao'], 'string'],
            [['iniciosintomas', 'datatriagem'], 'safe'],
            [['intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [['userprofile_id'], 'required'],
            [['motivoconsulta'], 'string', 'max' => 255],
            [['intensidadedor'], 'integer', 'min' => 0, 'max' => 10],
            [['prioridade_pulseira'], 'string'],

            // Relações
            [['pulseira_id'], 'exist', 'skipOnError' => true, 
                'targetClass' => Pulseira::class, 'targetAttribute' => ['pulseira_id' => 'id']],
            [['userprofile_id'], 'exist', 'skipOnError' => true, 
                'targetClass' => UserProfile::class, 'targetAttribute' => ['userprofile_id' => 'id']],
        ];
    }

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

    public function afterFind()
    {
        parent::afterFind();
        if (!empty($this->iniciosintomas) && $this->iniciosintomas !== '0000-00-00 00:00:00') {
            try {
                $date = new \DateTime($this->iniciosintomas);
                $this->iniciosintomas = $date->format('Y-m-d\TH:i');
            } catch (\Exception $e) {
            }
        }
    }

    public function getPulseira()
    {
        return $this->hasOne(Pulseira::class, ['id' => 'pulseira_id']);
    }

    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }
    
    public function getConsulta()
    {
        // Certifique-se que Consulta existe ou use caminho completo se falhar
        return $this->hasOne(\common\models\Consulta::class, ['triagem_id' => 'id']);
    }
}