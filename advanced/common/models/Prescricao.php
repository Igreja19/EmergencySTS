<?php

namespace common\models;

use Yii;
use common\models\Prescricaomedicamento;
use common\models\Medicamento;
use common\models\Consulta;

/**
 * This is the model class for table "prescricao".
 *
 * @property int $id
 * @property string $observacoes
 * @property string $dataprescricao
 * @property int $consulta_id
 *
 * @property Consulta $consulta
 * @property Prescricaomedicamento[] $prescricaomedicamentos
 */
class Prescricao extends \yii\db\ActiveRecord
{
    /**
     * IDs dos medicamentos selecionados no formulário
     * (para dropdown múltiplo)
     */
    public $medicamento_ids = [];

    public static function tableName()
    {
        return 'prescricao';
    }

    public function rules()
    {
        return [
            [['observacoes', 'consulta_id'], 'required'],
            [['observacoes'], 'string'],
            [['dataprescricao'], 'safe'],
            [['consulta_id'], 'integer'],

            // campo de seleção múltipla de medicamentos
            [['medicamento_ids'], 'safe'],

            [
                ['consulta_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Consulta::class,
                'targetAttribute' => ['consulta_id' => 'id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'observacoes' => 'Observações',
            'dataprescricao' => 'Data da Prescrição',
            'consulta_id' => 'Consulta',
            'medicamento_ids' => 'Medicamentos',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            // Se quiser preencher automaticamente
            // $this->dataprescricao = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    public function getConsulta()
    {
        return $this->hasOne(Consulta::class, ['id' => 'consulta_id']);
    }

    public function getPrescricaoMedicamentos()
    {
        return $this->hasMany(Prescricaomedicamento::class, ['prescricao_id' => 'id']);
    }

    public function getMedicamentos()
    {
        return $this->hasMany(Medicamento::class, ['id' => 'medicamento_id'])
            ->via('prescricaoMedicamentos');
    }
}
