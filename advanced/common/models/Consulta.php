<?php

namespace common\models;

use Yii;

class Consulta extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'consulta';
    }

    public function rules()
    {
        return [
            [['data_consulta', 'data_encerramento'], 'safe'],
            [['estado', 'observacoes'], 'string'],

            // 🔹 Apenas estes dois são obrigatórios no momento da criação
            [['userprofile_id', 'triagem_id'], 'required'],

            [['userprofile_id', 'triagem_id', 'prescricao_id'], 'integer'],

            [['relatorio_pdf'], 'string', 'max' => 255],

            // 🔹 Relações de integridade (verifica se existem)
            [['triagem_id'], 'exist', 'skipOnError' => true, 'targetClass' => Triagem::class, 'targetAttribute' => ['triagem_id' => 'id']],
            [['userprofile_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::class, 'targetAttribute' => ['userprofile_id' => 'id']],
            [['prescricao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prescricao::class, 'targetAttribute' => ['prescricao_id' => 'id']],
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
            'prescricao_id' => 'Prescrição',
            'data_encerramento' => 'Data de Encerramento',
            'relatorio_pdf' => 'Relatório PDF',
        ];
    }

    // 🔹 Relação com UserProfile
    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }

    // 🔹 Relação com Triagem
    public function getTriagem()
    {
        return $this->hasOne(Triagem::class, ['id' => 'triagem_id']);
    }

    // 🔹 Relação com Prescrição (pode ser nula)
    public function getPrescricao()
    {
        return $this->hasOne(Prescricao::class, ['id' => 'prescricao_id']);
    }
}
