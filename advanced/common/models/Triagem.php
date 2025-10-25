<?php

namespace common\models;

use frontend\models\Paciente;
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
            // 🔹 Campos obrigatórios no formulário
            [['nomecompleto', 'datanascimento', 'sns', 'telefone', 'motivoconsulta', 'queixaprincipal', 'prioridadeatribuida', 'datatriagem'], 'required'],

            // 🔹 Campos do tipo texto
            [['descricaosintomas', 'condicoes', 'alergias', 'medicacao', 'queixaprincipal'], 'string'],

            // 🔹 Campos de data/hora
            [['datatriagem', 'datanascimento', 'iniciosintomas'], 'safe'],

            // 🔹 Campos numéricos
            [['intensidadedor', 'utilizador_id', 'paciente_id'], 'integer'],

            // 🔹 Comprimentos máximos
            [['nomecompleto', 'motivoconsulta', 'discriminacaoprincipal'], 'string', 'max' => 100],
            [['sns', 'telefone'], 'string', 'max' => 20],

            // 🔹 Lista de prioridades válidas
            [['prioridadeatribuida'], 'in', 'range' => ['Vermelha', 'Laranja', 'Amarela', 'Verde', 'Azul']],
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
            'paciente_id' => 'Paciente',
            'utilizador_id' => 'Utilizador Responsável',
        ];
    }
    public function getPaciente()
    {
        return $this->hasOne(Paciente::class, ['id' => 'paciente_id']);
    }
}
