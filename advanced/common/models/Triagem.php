<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "triagem".
 *
 * @property int $id
 * @property string|null $motivoconsulta
 * @property string|null $queixaprincipal
 * @property string|null $descricaosintomas
 * @property string|null $iniciosintomas
 * @property int|null $intensidadedor
 * @property string|null $alergias
 * @property string|null $medicacao
 * @property string $motivo
 * @property string $datatriagem
 * @property int $userprofile_id
 * @property int $pulseira_id
 *
 * @property Consulta[] $consultas
 * @property Pulseira $pulseira
 * @property Userprofile $userprofile
 */
class Triagem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'triagem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['queixaprincipal', 'descricaosintomas', 'alergias', 'medicacao', 'motivo'], 'string'],
            [['iniciosintomas', 'datatriagem'], 'safe'],
            [['intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [['motivo', 'userprofile_id', 'pulseira_id'], 'required'],
            [['motivoconsulta'], 'string', 'max' => 255],
            [['pulseira_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pulseira::class, 'targetAttribute' => ['pulseira_id' => 'id']],
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
            'motivoconsulta' => 'Motivoconsulta',
            'queixaprincipal' => 'Queixaprincipal',
            'descricaosintomas' => 'Descricaosintomas',
            'iniciosintomas' => 'Iniciosintomas',
            'intensidadedor' => 'Intensidadedor',
            'alergias' => 'Alergias',
            'medicacao' => 'Medicacao',
            'motivo' => 'Motivo',
            'datatriagem' => 'Datatriagem',
            'userprofile_id' => 'Userprofile ID',
            'pulseira_id' => 'Pulseira ID',
        ];
    }

    /**
     * Gets query for [[Consultas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConsultas()
    {
        return $this->hasMany(Consulta::class, ['triagem_id' => 'id']);
    }

    /**
     * Gets query for [[Pulseira]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPulseira()
    {
        return $this->hasOne(Pulseira::class, ['id' => 'pulseira_id']);
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
}
