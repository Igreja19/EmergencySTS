<?php
namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

class SignupTest extends \Codeception\Test\Unit
{
    protected FunctionalTester $tester;

    public function testSignupComSucesso()
    {
        $I = $this->tester;

        $uid = uniqid();
        $username = 'test_' . $uid;
        $email = 'test_' . $uid . '@email.pt';

        $I->amOnRoute('site/signup');

        // Verifica o título da página
        $I->see('Criar Conta');
        $I->see('Crie a sua conta de paciente');

        // Preenche o formulário
        $I->fillField('SignupForm[username]', $username);
        $I->fillField('SignupForm[email]', $email);
        $I->fillField('SignupForm[password]', 'password_test_123');

        // Clica no botão verde "Criar Conta"
        $I->click('Criar Conta', 'form'); // Especifica que é o botão do formulário

        // Verifica se o formulário desapareceu (sucesso)
        $I->dontSee('Crie a sua conta de paciente', 'form');

        // Confirma na base de dados
        $I->seeRecord('common\models\User', [
            'username' => $username,
            'email' => $email
        ]);
    }
}