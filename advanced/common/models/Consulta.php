<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Consulta extends ActiveRecord
{
    /**
     * Nome da tabela
     */
    public static function tableName()
    {
        return 'consulta';
    }

    /**
     * Regras de validação
     */
    public function rules()
    {
        return [
            [['data_consulta', 'estado', 'paciente_id', 'utilizador_id', 'triagem_id'], 'required'],
            [['data_consulta', 'data_encerramento'], 'safe'],
            [['diagnostico_id', 'paciente_id', 'utilizador_id', 'triagem_id'], 'integer'],
            [['estado', 'prioridade'], 'string'],
            [['motivo', 'tempo_consulta', 'relatorio_pdf'], 'string', 'max' => 255],
            [['observacoes'], 'string'],
        ];
    }

    /**
     * Rótulos (labels) usados nas views
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_consulta' => 'Data da Consulta',
            'estado' => 'Estado',
            'prioridade' => 'Prioridade',
            'motivo' => 'Motivo da Consulta',
            'observacoes' => 'Observações',
            'paciente_id' => 'Paciente',
            'utilizador_id' => 'Utilizador',
            'triagem_id' => 'Triagem',
            'diagnostico_id' => 'Diagnóstico',
            'data_encerramento' => 'Data de Encerramento',
            'tempo_consulta' => 'Tempo de Consulta',
            'relatorio_pdf' => 'Relatório PDF',
        ];
    }

    // 🔹 Relação com a triagem
    public function getTriagem()
    {
        return $this->hasOne(Triagem::class, ['id' => 'triagem_id']);
    }

    // 🔹 Relação com o paciente
    public function getUserprofile()
    {
        return $this->hasOne(Userprofile::class, ['id' => 'userprofile_id']);
    }

    // 🔹 Relação com o utilizador (médico/enfermeiro)
    public function getUtilizador()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'utilizador_id']);
    }
}
