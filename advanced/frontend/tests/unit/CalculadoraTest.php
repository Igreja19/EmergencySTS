<?php

namespace frontend\tests\Unit;

use frontend\tests\UnitTester;
use common\models\Calculadora; // Importante: Importar a classe que vamos testar

class CalculadoraTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    // Propriedade para guardar a instância da calculadora
    private $calculadora;

    protected function _before()
    {
        // Instancia a classe antes de cada teste correr
        $this->calculadora = new Calculadora();
    }

    // --- Ponto 3: Testes Unitários Simples ---

    public function testSomar()
    {
        // Teste: 5 + 3 = 8
        $resultado = $this->calculadora->somar(5, 3);
        $this->assertEquals(8, $resultado);
    }

    public function testSubtrair()
    {
        // Teste: 10 - 4 = 6
        $resultado = $this->calculadora->subtrair(10, 4);
        $this->assertEquals(6, $resultado);
    }

    public function testMultiplicar()
    {
        // Teste: 3 * 3 = 9
        $resultado = $this->calculadora->multiplicar(3, 3);
        $this->assertEquals(9, $resultado);
    }

    public function testDividir()
    {
        // Teste: 10 / 2 = 5
        $resultado = $this->calculadora->dividir(10, 2);
        $this->assertEquals(5, $resultado);
    }

    // --- Ponto 4: Teste de Falha (Exceção) ---

    public function testDividirPorZero()
    {
        // Dizemos ao teste que ESPERAMOS que aconteça um erro (Exceção)
        $this->expectException(\InvalidArgumentException::class);

        // Executamos a ação proibida
        $this->calculadora->dividir(10, 0);
    }
}