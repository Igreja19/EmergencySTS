<?php

namespace common\models;

use Yii;
use common\models\UserProfile;
use common\models\Pulseira;

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
 * @property UserProfile $userprofile
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
            [['iniciosintomas', 'datatriagem', 'prioridade_pulseira'], 'safe'],
            [['intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [['userprofile_id'], 'required'],
            [['motivoconsulta'], 'string', 'max' => 255],
            [['intensidadedor'], 'integer', 'min' => 0, 'max' => 10],
            [['prioridade_pulseira'], 'string'],

            // Relações
            [
                ['pulseira_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Pulseira::class,
                'targetAttribute' => ['pulseira_id' => 'id']
            ],
            [
                ['userprofile_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UserProfile::class,
                'targetAttribute' => ['userprofile_id' => 'id']
            ],
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

    /** FORMATAÇÃO AUTOMÁTICA DA DATA AO LER */
    public function afterFind()
    {
        parent::afterFind();
        if (!empty($this->iniciosintomas) && $this->iniciosintomas !== '0000-00-00 00:00:00') {
            try {
                $date = new \DateTime($this->iniciosintomas);
                $this->iniciosintomas = $date->format('Y-m-d\TH:i');
            } catch (\Exception $e) {}
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->datatriagem = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    /** RELAÇÕES */

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
        return $this->hasOne(\common\models\Consulta::class, ['triagem_id' => 'id']);
    }
    public function getInicioSintomasFormatado()
    {
        return Yii::$app->formatter->asDatetime($this->iniciosintomas, 'php:d/m/Y H:i');
    }

    /** CAMPOS DEVOLVIDOS SEMPRE NO JSON */
    public function fields()
    {
        $fields = parent::fields();

        // Remover os IDs porque já vais enviar objetos completos
        unset($fields['userprofile_id'], $fields['pulseira_id']);

        // Incluir automaticamente as relações
        $fields['userprofile'] = function ($model) {
            return $model->userprofile;
        };

        $fields['pulseira'] = function ($model) {
            return $model->pulseira;
        };

        return $fields;
    }
}
