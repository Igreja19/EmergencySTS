<?php

namespace common\models;

/**
 * This is the model class for table "consulta".
 *
 * @property int $id
 * @property string $data_consulta
 * @property string $estado
 * @property int $diagnostico_id
 * @property int $paciente_id
 * @property int $utilizador_id
 */
class Consulta extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_ABERTA = 'Aberta';
    const ESTADO_ENCERRADA = 'Encerrada';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consulta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'default', 'value' => 'Aberta'],
            [['id', 'diagnostico_id', 'paciente_id', 'utilizador_id'], 'required'],
            [['id', 'diagnostico_id', 'paciente_id', 'utilizador_id'], 'integer'],
            [['data_consulta'], 'safe'],
            [['estado'], 'string'],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
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
            'data_consulta' => 'Data Consulta',
            'estado' => 'Estado',
            'diagnostico_id' => 'Diagnostico ID',
            'paciente_id' => 'Paciente ID',
            'utilizador_id' => 'Utilizador ID',
        ];
    }


    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_ABERTA => 'Aberta',
            self::ESTADO_ENCERRADA => 'Encerrada',
        ];
    }

    /**
     * @return string
     */
    public function displayEstado()
    {
        return self::optsEstado()[$this->estado];
    }

    /**
     * @return bool
     */
    public function isEstadoAberta()
    {
        return $this->estado === self::ESTADO_ABERTA;
    }

    public function setEstadoToAberta()
    {
        $this->estado = self::ESTADO_ABERTA;
    }

    /**
     * @return bool
     */
    public function isEstadoEncerrada()
    {
        return $this->estado === self::ESTADO_ENCERRADA;
    }

    public function setEstadoToEncerrada()
    {
        $this->estado = self::ESTADO_ENCERRADA;
    }
}
