<?php
namespace common\models;

use yii\base\Model;
use InvalidArgumentException;

class Calculadora extends Model
{
    public function somar($num1, $num2)
    {
        return $num1 + $num2;
    }

    public function subtrair($num1, $num2)
    {
        return $num1 - $num2;
    }

    public function multiplicar($num1, $num2)
    {
        return $num1 * $num2;
    }

    public function dividir($num1, $num2)
    {
        // Ponto 4: Tratamento da falha (Divisão por zero)
        if ($num2 == 0) {
            throw new InvalidArgumentException("Não é possível dividir por zero.");
        }
        return $num1 / $num2;
    }
}