<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $nomecompleto
 * @property string $nif
 * @property string $datanascimento
 * @property string|null $sns
 * @property string $genero
 * @property string $telefone
 * @property string|null $email
 * @property string $morada
 * @property string|null $observacoes
 *
 * @property Consulta[] $consultas
 * @property Pulseira[] $pulseiras
 * @property Triagem[] $triagens
 */
class Paciente extends ActiveRecord
{
    public static function tableName()
    {
        return 'paciente';
    }

    public function rules()
    {
        return [
            [['nomecompleto', 'nif', 'datanascimento', 'genero', 'telefone', 'morada'], 'required'],
            [['observacoes'], 'string'],
            [['datanascimento'], 'date', 'format' => 'php:Y-m-d'], // a BD guarda DATE
            [['nomecompleto'], 'string', 'max' => 100],
            [['sns'], 'string', 'max' => 20],
            [['telefone'], 'string', 'max' => 15],
            [['morada'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['nif'], 'string', 'max' => 9],
            [['nif'], 'match', 'pattern' => '/^\d{3,9}$/', 'message' => 'NIF deve conter apenas dígitos (3–9).'],
            [['nif'], 'unique'],
            [['genero'], 'in', 'range' => ['Masculino','Feminino','Outro']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nomecompleto'   => 'Nome completo',
            'nif'            => 'NIF',
            'datanascimento' => 'Data de nascimento',
            'sns'            => 'SNS',
            'genero'         => 'Género',
            'telefone'       => 'Telefone',
            'email'          => 'Email',
            'morada'         => 'Morada',
            'observacoes'    => 'Observações',
        ];
    }

    // Relações
    public function getConsultas()
    {
        return $this->hasMany(Consulta::class, ['paciente_id' => 'id']);
    }

    public function getPulseiras()
    {
        return $this->hasMany(Pulseira::class, ['paciente_id' => 'id']);
    }

    public function getTriagens()
    {
        return $this->hasMany(Triagem::class, ['paciente_id' => 'id']);
    }
}
