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
 * @property UserProfile $userprofile   ← CORRETO AGORA
 */
class Consulta extends \yii\db\ActiveRecord
{
    /**
     * ENUM VALUES
     */
    const ESTADO_ENCERRADA = 'Encerrada';
    const ESTADO_EM_CURSO = 'Em curso';

    public static function tableName()
    {
        return 'consulta';
    }

    public function rules()
    {
        return [

            [['observacoes', 'data_encerramento', 'relatorio_pdf'], 'default', 'value' => null],

            [['estado'], 'default', 'value' => self::ESTADO_EM_CURSO],

            [['data_consulta', 'data_encerramento'], 'safe'],

            [['estado', 'observacoes'], 'string'],

            [['userprofile_id', 'triagem_id', 'medicouserprofile_id'], 'required'],

            [['userprofile_id', 'triagem_id', 'medicouserprofile_id'], 'integer'],

            [['relatorio_pdf'], 'string', 'max' => 255],

            ['estado', 'in', 'range' => array_keys(self::optsEstado())],

            [['triagem_id'], 'exist', 'skipOnError' => true, 'targetClass' => Triagem::class, 'targetAttribute' => ['triagem_id' => 'id']],
            [['userprofile_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::class, 'targetAttribute' => ['userprofile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_consulta' => 'Data da Consulta',
            'estado' => 'Estado',
            'observacoes' => 'Observações',
            'userprofile_id' => 'Paciente',
            'triagem_id' => 'Triagem',
            'data_encerramento' => 'Data Encerramento',
            'relatorio_pdf' => 'Relatório PDF',
        ];
    }

    public function getPrescricaos()
    {
        return $this->hasMany(Prescricao::class, ['consulta_id' => 'id']);
    }

    public function getPrescricoes()
    {
        return $this->hasMany(Prescricao::class, ['consulta_id' => 'id']);
    }

    public function getPrescricao()
    {
        return $this->hasOne(Prescricao::class, ['consulta_id' => 'id']);
    }

    public function getTriagem()
    {
        return $this->hasOne(Triagem::class, ['id' => 'triagem_id']);
    }

    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }

    public function getMedico()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'medicouserprofile_id']);
    }

    public static function optsEstado()
    {
        return [
            self::ESTADO_ENCERRADA => 'Encerrada',
            self::ESTADO_EM_CURSO => 'Em curso',
        ];
    }

    public function displayEstado()
    {
        return self::optsEstado()[$this->estado];
    }

    public function isEstadoEncerrada()
    {
        return $this->estado === self::ESTADO_ENCERRADA;
    }

    public function setEstadoToEncerrada()
    {
        $this->estado = self::ESTADO_ENCERRADA;
        $this->data_encerramento = date('Y-m-d H:i:s');
    }

    public function isEstadoEmCurso()
    {
        return $this->estado === self::ESTADO_EM_CURSO;
    }

    public function setEstadoToEmCurso()
    {
        $this->estado = self::ESTADO_EM_CURSO;
        $this->data_encerramento = null;
    }
    public function beforeSave($insert)
    {
        if ($this->data_encerramento === '-' || $this->data_encerramento === '') {
            $this->data_encerramento = null;
        }

        return parent::beforeSave($insert);
    }
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (empty($this->data_consulta)) {
                $this->data_consulta = date('Y-m-d H:i:s');
            }
        }
        return parent::beforeValidate();
    }
}
