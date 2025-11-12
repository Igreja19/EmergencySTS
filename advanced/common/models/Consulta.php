<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "consulta".
 *
 * @property int $id
 * @property string $data_consulta
 * @property string $estado
 * @property string|null $observacoes
 * @property int $userprofile_id
 * @property int $triagem_id
 * @property string|null $data_encerramento
 * @property string|null $relatorio_pdf
 *
 * @property Prescricao[] $prescricaos
 * @property Triagem $triagem
 * @property Userprofile $userprofile
 */
class Consulta extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_ABERTA = 'Aberta';
    const ESTADO_ENCERRADA = 'Encerrada';
    const ESTADO_EM_CURSO = 'Em curso';

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
            [['observacoes', 'data_encerramento', 'relatorio_pdf'], 'default', 'value' => null],
            [['estado'], 'default', 'value' => 'Aberta'],
            [['data_consulta', 'data_encerramento'], 'safe'],
            [['estado', 'observacoes'], 'string'],
            [['userprofile_id', 'triagem_id'], 'required'],
            [['userprofile_id', 'triagem_id'], 'integer'],
            [['relatorio_pdf'], 'string', 'max' => 255],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
            [['triagem_id'], 'exist', 'skipOnError' => true, 'targetClass' => Triagem::class, 'targetAttribute' => ['triagem_id' => 'id']],
            [['userprofile_id'], 'exist', 'skipOnError' => true, 'targetClass' => Userprofile::class, 'targetAttribute' => ['userprofile_id' => 'id']],
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
            'observacoes' => 'Observacoes',
            'userprofile_id' => 'Userprofile ID',
            'triagem_id' => 'Triagem ID',
            'data_encerramento' => 'Data Encerramento',
            'relatorio_pdf' => 'Relatorio Pdf',
        ];
    }

    /**
     * Gets query for [[Prescricaos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrescricaos()
    {
        return $this->hasMany(Prescricao::class, ['consulta_id' => 'id']);
    }

    /**
     * Gets query for [[Triagem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTriagem()
    {
        return $this->hasOne(Triagem::class, ['id' => 'triagem_id']);
    }

    /**
     * Gets query for [[Userprofile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserprofile()
    {
        return $this->hasOne(Userprofile::class, ['id' => 'userprofile_id']);
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
            self::ESTADO_EM_CURSO => 'Em curso',
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

    /**
     * @return bool
     */
    public function isEstadoEmCurso()
    {
        return $this->estado === self::ESTADO_EM_CURSO;
    }

    public function setEstadoToEmCurso()
    {
        $this->estado = self::ESTADO_EM_CURSO;
    }
}
