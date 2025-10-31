<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "consulta".
 *
 * @property int $id
 * @property string $data_consulta
 * @property string $estado
 * @property string|null $prioridade
 * @property string|null $motivo
 * @property string|null $observacoes
 * @property int $userprofile_id
 * @property int $triagem_id
 * @property int $prescricao_id
 * @property string|null $data_encerramento
 * @property string|null $tempo_consulta
 * @property string|null $relatorio_pdf
 *
 * @property Prescricao $prescricao
 * @property Triagem $triagem
 * @property User $userprofile
 */
class Consulta extends \yii\db\ActiveRecord
{
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
            [['data_consulta', 'data_encerramento'], 'safe'],
            [['estado', 'prioridade', 'observacoes'], 'string'],
            [['userprofile_id', 'triagem_id', 'prescricao_id'], 'required'],
            [['userprofile_id', 'triagem_id', 'prescricao_id'], 'integer'],
            [['motivo', 'relatorio_pdf'], 'string', 'max' => 255],
            [['tempo_consulta'], 'string', 'max' => 50],
            [['triagem_id'], 'exist', 'skipOnError' => true, 'targetClass' => Triagem::class, 'targetAttribute' => ['triagem_id' => 'id']],
            [['userprofile_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userprofile_id' => 'id']],
            [['prescricao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prescricao::class, 'targetAttribute' => ['prescricao_id' => 'id']],
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
            'prioridade' => 'Prioridade',
            'motivo' => 'Motivo',
            'observacoes' => 'Observacoes',
            'userprofile_id' => 'UserProfile ID',
            'triagem_id' => 'Triagem ID',
            'prescricao_id' => 'Prescricao ID',
            'data_encerramento' => 'Data Encerramento',
            'tempo_consulta' => 'Tempo Consulta',
            'relatorio_pdf' => 'Relatorio Pdf',
        ];
    }

    /**
     * Gets query for [[Prescricao]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrescricao()
    {
        return $this->hasOne(Prescricao::class, ['id' => 'prescricao_id']);
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
     * Gets query for [[UserProfile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserprofile()
    {
        return $this->hasOne(User::class, ['id' => 'userprofile_id']);
    }
}
