<?php
namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\models\User;

class LoginTest extends \Codeception\Test\Unit
{
    protected FunctionalTester $tester;

    // Removemos o _before com fixtures para criar o user manualmente no teste
    // Assim temos certeza absoluta que a password está correta

    public function testLoginComSucesso()
    {
        $I = $this->tester;

        // 1. CRIAR UTILIZADOR DE TESTE
        // Criamos um user novo na BD especificamente para este teste
        $password = '123456'; // Password conhecida

        $user = new User();
        $user->username = 'login_teste_' . uniqid(); // Nome único
        $user->email = 'login_' . uniqid() . '@teste.com';
        $user->status = User::STATUS_ACTIVE; // GARANTIR QUE ESTÁ ATIVO
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->save(false); // Salvar sem validar para ser rápido

        // 2. NAVEGAR
        $I->amOnPage('/site/login');
        $I->see('Iniciar Sessão'); //

        // 3. PREENCHER
        $I->fillField('LoginForm[username]', $user->username);
        $I->fillField('LoginForm[password]', $password);

        // 4. SUBMETER
        $I->click('Entrar'); //

        // 5. VERIFICAR SUCESSO
        // Se o login funcionar, o formulário de login desaparece
        $I->dontSee('Iniciar Sessão');
        $I->dontSee('Incorrect username or password');

        // Verifica se o nome do utilizador aparece no menu (sinal que está logado)
        // Nota: O menu pode mostrar "Logout (username)"
        $I->see($user->username);
    }
}