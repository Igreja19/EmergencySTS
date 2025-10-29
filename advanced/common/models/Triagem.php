<?php

namespace common\models;

// REMOVE: use frontend\models\Paciente;
use yii\db\ActiveRecord;

class Triagem extends ActiveRecord
{
    public static function tableName()
    {
        return 'triagem';
    }

    public function rules()
    {
        return [
            [['nomecompleto', 'motivoconsulta', 'prioridadeatribuida'], 'required'],
            [['queixaprincipal', 'descricaosintomas', 'condicoes', 'alergias', 'medicacao', 'motivo'], 'string'],
            [['datatriagem', 'datanascimento', 'iniciosintomas'], 'safe'],
            [['intensidadedor', 'utilizador_id', 'paciente_id'], 'integer'],
            [['nomecompleto', 'motivoconsulta', 'discriminacaoprincipal'], 'string', 'max' => 100],
            [['sns', 'telefone'], 'string', 'max' => 20],
            // CORRIGIDO: 'Vermelha' -> 'Vermelho'
            [['prioridadeatribuida'], 'in', 'range' => ['Vermelho','Laranja','Amarelo','Verde','Azul']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nomecompleto' => 'Nome Completo',
            'datanascimento' => 'Data de Nascimento',
            'sns' => 'Número de Utente (SNS)',
            'telefone' => 'Telefone',
            'motivoconsulta' => 'Motivo da Consulta',
            'queixaprincipal' => 'Queixa Principal',
            'descricaosintomas' => 'Descrição dos Sintomas',
            'iniciosintomas' => 'Início dos Sintomas',
            'intensidadedor' => 'Intensidade da Dor (0-10)',
            'condicoes' => 'Condições Médicas Conhecidas',
            'alergias' => 'Alergias Conhecidas',
            'medicacao' => 'Medicação Atual',
            'discriminacaoprincipal' => 'Discriminação Principal',
            'prioridadeatribuida' => 'Prioridade Atribuída',
            'datatriagem' => 'Data da Triagem',
            'paciente_id' => 'User (antigo paciente)',
            'utilizador_id' => 'Utilizador Responsável',
        ];
    }

    public function getConsulta()
    {
        return $this->hasOne(\common\models\Consulta::class, ['triagem_id' => 'id']);
    }
    public function getUserprofile()
    {
        return $this->hasOne(Userprofile::class, ['id' => 'paciente_id']);
    }
}
