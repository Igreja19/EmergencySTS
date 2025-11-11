<?php

namespace common\models;

use Yii;

/**
 * Esta é a classe modelo para a tabela "prescricao".
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
    public static function tableName()
    {
        return 'prescricao';
    }

    public function rules()
    {
        return [
            [['observacoes', 'consulta_id'], 'required'],
            [['consulta_id'], 'integer'],
            [['dataprescricao'], 'safe'],
            [['observacoes'], 'string', 'max' => 255],
            [['consulta_id'], 'exist', 'skipOnError' => true, 'targetClass' => Consulta::class, 'targetAttribute' => ['consulta_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'observacoes' => 'Observações',
            'dataprescricao' => 'Data da Prescrição',
            'consulta_id' => 'Consulta Associada',
        ];
    }

    // 🔹 Relação: cada prescrição pertence a uma consulta
    public function getConsulta()
    {
        return $this->hasOne(Consulta::class, ['id' => 'consulta_id']);
    }

    // 🔹 Relação: cada prescrição pode ter vários medicamentos
    public function getPrescricaomedicamentos()
    {
        return $this->hasMany(Prescricaomedicamento::class, ['prescricao_id' => 'id']);
    }

    // 🔹 Acesso rápido aos medicamentos através da tabela relacional
    public function getMedicamentos()
    {
        return $this->hasMany(Medicamento::class, ['id' => 'medicamento_id'])
            ->via('prescricaomedicamentos');
    }
}
