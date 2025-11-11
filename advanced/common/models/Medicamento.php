<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Esta é a classe modelo para a tabela "medicamento".
 *
 * @property int $id
 * @property string $nome
 * @property string $dosagem
 *
 * @property Prescricaomedicamento[] $prescricaomedicamentos
 */
class Medicamento extends ActiveRecord
{
    public static function tableName()
    {
        return 'medicamento';
    }

    public function rules()
    {
        return [
            [['nome', 'dosagem'], 'required'],
            [['nome', 'dosagem'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome do Medicamento',
            'dosagem' => 'Dosagem',
        ];
    }

    // 🔹 Relação com prescricaomedicamento
    public function getPrescricaomedicamentos()
    {
        return $this->hasMany(Prescricaomedicamento::class, ['medicamento_id' => 'id']);
    }
}
