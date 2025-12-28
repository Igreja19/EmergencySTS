<?php
namespace frontend\tests\unit;

use frontend\tests\UnitTester;
use common\models\Pessoa;

class PessoaTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    public function testValidacaoInvalida()
    {
        $pessoa = new Pessoa();
        $pessoa->nome = " ";
        $pessoa->email = "email-sem-arroba";
        $pessoa->nif = "123";

        $this->assertFalse($pessoa->validate());
        $this->assertArrayHasKey('nome', $pessoa->errors);
        $this->assertArrayHasKey('email', $pessoa->errors);
        $this->assertArrayHasKey('nif', $pessoa->errors);
    }

    public function testCicloDeVidaNaBD()
    {
        $nifTeste = '999999999';

        // 1. Limpeza preventiva (ActiveRecord)
        $lixo = Pessoa::findOne(['nif' => $nifTeste]);
        if ($lixo) {
            $lixo->delete();
        }

        // 2. Criar
        $pessoa = new Pessoa();
        $pessoa->nome = 'Ana Teste';
        $pessoa->idade = 25;
        $pessoa->morada = 'Rua do Codeception, 10';
        $pessoa->nif = $nifTeste;
        $pessoa->email = 'ana@exemplo.com';

        // 3. Guardar
        $this->assertTrue($pessoa->save(), 'Falha ao guardar: ' . json_encode($pessoa->getErrors()));

        // 4. Verificar existência (USANDO AR EM VEZ DE TESTER) - ISTO RESOLVE O SEU ERRO
        $pessoaNaBD = Pessoa::findOne(['nif' => $nifTeste]);
        $this->assertNotNull($pessoaNaBD, 'A pessoa deveria estar na base de dados');

        // 5. Update
        $pessoaNaBD->nome = 'Ana Atualizada';
        $this->assertTrue($pessoaNaBD->save(), 'Falha ao atualizar');

        // 6. Verificar atualização
        $pessoaAtualizada = Pessoa::findOne(['nif' => $nifTeste, 'nome' => 'Ana Atualizada']);
        $this->assertNotNull($pessoaAtualizada, 'O registo deveria existir');
        $this->assertEquals('Ana Atualizada', $pessoaAtualizada->nome);

        // 7. Apagar
        $pessoaAtualizada->delete();

        // 8. Verificar que sumiu
        $this->assertNull(Pessoa::findOne(['nif' => $nifTeste]), 'A pessoa deveria ter sido apagada');
    }
}