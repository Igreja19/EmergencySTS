<?php

namespace common\models;

/**
 * This is the model class for table "notificacao".
 *
 * @property int $id
 * @property string $mensagem
 * @property string $tipo
 * @property string $dataenvio
 * @property int $lida
 * @property int $paciente_id
 */
class Notificacao extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TIPO_CONSULTA = 'Consulta';
    const TIPO_PRIORIDADE = 'Prioridade';
    const TIPO_GERAL = 'Geral';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'default', 'value' => 'Geral'],
            [['lida'], 'default', 'value' => 0],
            [['id', 'mensagem', 'paciente_id'], 'required'],
            [['id', 'lida', 'paciente_id'], 'integer'],
            [['mensagem', 'tipo'], 'string'],
            [['dataenvio'], 'safe'],
            ['tipo', 'in', 'range' => array_keys(self::optsTipo())],
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
            'mensagem' => 'Mensagem',
            'tipo' => 'Tipo',
            'dataenvio' => 'Dataenvio',
            'lida' => 'Lida',
            'paciente_id' => 'Paciente ID',
        ];
    }


    /**
     * column tipo ENUM value labels
     * @return string[]
     */
    public static function optsTipo()
    {
        return [
            self::TIPO_CONSULTA => 'Consulta',
            self::TIPO_PRIORIDADE => 'Prioridade',
            self::TIPO_GERAL => 'Geral',
        ];
    }

    /**
     * @return string
     */
    public function displayTipo()
    {
        return self::optsTipo()[$this->tipo];
    }

    /**
     * @return bool
     */
    public function isTipoConsulta()
    {
        return $this->tipo === self::TIPO_CONSULTA;
    }

    public function setTipoToConsulta()
    {
        $this->tipo = self::TIPO_CONSULTA;
    }

    /**
     * @return bool
     */
    public function isTipoPrioridade()
    {
        return $this->tipo === self::TIPO_PRIORIDADE;
    }

    public function setTipoToPrioridade()
    {
        $this->tipo = self::TIPO_PRIORIDADE;
    }

    /**
     * @return bool
     */
    public function isTipoGeral()
    {
        return $this->tipo === self::TIPO_GERAL;
    }

    public function setTipoToGeral()
    {
        $this->tipo = self::TIPO_GERAL;
    }
}
