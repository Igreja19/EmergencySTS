<?php

namespace common\models;

/**
 * This is the model class for table "prescricao".
 *
 * @property int $id
 * @property string $medicamento
 * @property string $dosagem
 * @property string $frequencia
 * @property string $observacoes
 * @property string $dataprescricao
 * @property int $consulta_id
 */
class Prescricao extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prescricao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'medicamento', 'dosagem', 'frequencia', 'observacoes', 'consulta_id'], 'required'],
            [['id', 'consulta_id'], 'integer'],
            [['observacoes'], 'string'],
            [['dataprescricao'], 'safe'],
            [['medicamento', 'dosagem', 'frequencia'], 'string', 'max' => 100],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'medicamento' => 'Medicamento',
            'dosagem' => 'Dosagem',
            'frequencia' => 'Frequencia',
            'observacoes' => 'Observacoes',
            'dataprescricao' => 'Dataprescricao',
            'consulta_id' => 'Consulta ID',
        ];
    }

}
