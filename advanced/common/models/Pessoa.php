<?php
namespace common\models;

use yii\db\ActiveRecord;

class Pessoa extends ActiveRecord
{
    public static function tableName()
    {
        return 'pessoa';
    }

    public function rules()
    {
        return [
            // Campos obrigatórios
            [['nome', 'nif', 'email'], 'required'],

            // Validações de tipo
            [['idade'], 'integer'],
            [['email'], 'email'], // Garante que tem formato de email (tem @)

            // Validações de tamanho (max 80 caracteres)
            [['nome', 'morada'], 'string', 'max' => 80],

            // Validação do NIF (9 dígitos e único)
            [['nif'], 'string', 'length' => 9],
            [['nif'], 'unique', 'message' => 'Este NIF já existe na base de dados.'],
        ];
    }
}