<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Esta é a classe modelo para a tabela "prescricaomedicamento".
 *
 * @property int $id
 * @property string $posologia
 * @property int $prescricao_id
 * @property int $medicamento_id
 *
 * @property Prescricao $prescricao
 * @property Medicamento $medicamento
 */
class Prescricaomedicamento extends ActiveRecord
{
    public static function tableName()
    {
        return 'prescricaomedicamento';
    }

    public function rules()
    {
        return [
            [['posologia', 'prescricao_id', 'medicamento_id'], 'required'],
            [['prescricao_id', 'medicamento_id'], 'integer'],
            [['posologia'], 'string', 'max' => 255],
            [['prescricao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prescricao::class, 'targetAttribute' => ['prescricao_id' => 'id']],
            [['medicamento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Medicamento::class, 'targetAttribute' => ['medicamento_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'posologia' => 'Posologia',
            'prescricao_id' => 'Prescrição',
            'medicamento_id' => 'Medicamento',
        ];
    }

    public function getPrescricao()
    {
        return $this->hasOne(Prescricao::class, ['id' => 'prescricao_id']);
    }

    public function getMedicamento()
    {
        return $this->hasOne(Medicamento::class, ['id' => 'medicamento_id']);
    }
}
