<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pulseira".
 *
 * @property int $id
 * @property string $codigo
 * @property string $prioridade
 * @property string|null $status
 * @property string $tempoentrada
 *
 * @property Triagem[] $triagems
 */
class Pulseira extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pulseira';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'prioridade'], 'required'],
            [['prioridade', 'status'], 'string'],
            [['tempoentrada'], 'safe'],
            [['codigo'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'prioridade' => 'Prioridade',
            'status' => 'Status',
            'tempoentrada' => 'Tempoentrada',
        ];
    }

    /**
     * Gets query for [[Triagems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTriagems()
    {
        return $this->hasMany(Triagem::class, ['pulseira_id' => 'id']);
    }
}
