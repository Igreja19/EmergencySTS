<?php
namespace common\models;

use frontend\models\Paciente;
use yii\db\ActiveRecord;

class Pulseira extends ActiveRecord
{
    public static function tableName()
    {
        return 'pulseira';
    }

    public function rules()
    {
        return [
            // 🔹 Campos obrigatórios
            [['codigo', 'prioridade', 'triagem_id'], 'required'],

            // 🔹 Campos seguros
            [['tempoentrada'], 'safe'],

            // 🔹 Inteiros
            [['triagem_id', 'paciente_id'], 'integer'],

            // 🔹 Comprimentos máximos
            [['codigo'], 'string', 'max' => 10],
            [['prioridade'], 'in', 'range' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código da Pulseira',
            'prioridade' => 'Cor da Prioridade',
            'tempoentrada' => 'Hora de Entrada',
            'triagem_id' => 'Triagem Associada',
            'paciente_id' => 'Paciente',
        ];
    }

    // 🔹 Relação com a triagem
    public function getTriagem()
    {
        return $this->hasOne(Triagem::class, ['id' => 'triagem_id']);
    }

    // 🔹 Relação com o paciente (se existir tabela paciente)
    public function getPaciente()
    {
        return $this->hasOne(Paciente::class, ['id' => 'paciente_id']);
    }
}
