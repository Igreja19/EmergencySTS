<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notificacao".
 *
 * @property int $id
 * @property string|null $titulo
 * @property string $mensagem
 * @property string $tipo
 * @property string $dataenvio
 * @property int $lida
 * @property int $userprofile_id
 *
 * @property Userprofile $userprofile
 */
class Notificacao extends \yii\db\ActiveRecord
{
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
            [['mensagem', 'userprofile_id'], 'required'],
            [['mensagem', 'tipo'], 'string'],
            [['dataenvio'], 'safe'],
            [['lida', 'userprofile_id'], 'integer'],
            [['titulo'], 'string', 'max' => 150],
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
            'titulo' => 'Titulo',
            'mensagem' => 'Mensagem',
            'tipo' => 'Tipo',
            'dataenvio' => 'Dataenvio',
            'lida' => 'Lida',
            'userprofile_id' => 'Userprofile ID',
        ];
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
