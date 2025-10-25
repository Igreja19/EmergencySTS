<?php

namespace common\models;

/**
 * This is the model class for table "observacao".
 *
 * @property int $id
 * @property string $descricao
 * @property string $sintomas
 * @property string $dataregisto
 * @property int $consulta_id
 */
class Observacao extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'observacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'descricao', 'sintomas', 'consulta_id'], 'required'],
            [['id', 'consulta_id'], 'integer'],
            [['sintomas'], 'string'],
            [['dataregisto'], 'safe'],
            [['descricao'], 'string', 'max' => 255],
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
            'descricao' => 'Descricao',
            'sintomas' => 'Sintomas',
            'dataregisto' => 'Dataregisto',
            'consulta_id' => 'Consulta ID',
        ];
    }

}
