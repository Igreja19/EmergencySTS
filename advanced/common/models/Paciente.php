<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Esta é a classe modelo para a tabela "paciente".
 *
 * @property int $id
 * @property string $nomecompleto
 * @property string|null $datanascimento
 * @property string|null $sns
 * @property string|null $telefone
 * @property string|null $email
 * @property string|null $morada
 * @property string|null $genero
 * @property string|null $nif
 * @property string|null $observacoes
 */
class Paciente extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paciente';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomecompleto'], 'required'],
            [['datanascimento'], 'safe'],
            [['observacoes'], 'string'],
            [['nomecompleto'], 'string', 'max' => 100],
            [['sns', 'telefone', 'nif'], 'string', 'max' => 20],
            [['email', 'morada'], 'string', 'max' => 255],
            [['genero'], 'in', 'range' => ['Masculino', 'Feminino', 'Outro']],
            [['sns'], 'unique', 'message' => 'O número SNS já está registado.'],
            [['email'], 'email'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nomecompleto' => 'Nome Completo',
            'datanascimento' => 'Data de Nascimento',
            'sns' => 'Número de Utente (SNS)',
            'telefone' => 'Telefone',
            'email' => 'Email',
            'morada' => 'Morada',
            'genero' => 'Género',
            'nif' => 'NIF',
            'observacoes' => 'Observações',
        ];
    }

    /**
     * Relações com outras tabelas
     */

    // 🔹 Relação com Triagem
    public function getTriagens()
    {
        return $this->hasMany(Triagem::class, ['paciente_id' => 'id']);
    }

    // 🔹 Relação com Pulseira
    public function getPulseiras()
    {
        return $this->hasMany(Pulseira::class, ['paciente_id' => 'id']);
    }
}