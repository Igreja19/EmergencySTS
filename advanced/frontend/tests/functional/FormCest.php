<?php

namespace frontend\tests\Functional;

use common\models\User;
use common\models\UserProfile;
use frontend\tests\FunctionalTester;
use Yii;

class FormCest extends \Codeception\Test\Unit
{

    protected FunctionalTester $tester;

    protected function _before()
    {
    }
    /* =====================================================
     * TESTE 1 — Criar utilizador paciente e fazer login
     * ===================================================== */
    public function pacienteConsegueFazerLogin(FunctionalTester $I)
    {
        $this->criarPaciente();

        $this->loginPaciente($I);

        $I->see('Logout');
    }

    /* =====================================================
     * TESTE 2 — Primeiro login e completar perfil
     * ===================================================== */
    public function pacienteCompletaPerfilNoPrimeiroLogin(FunctionalTester $I)
    {
        $user = $this->garantirPacienteExiste();

        // Garantir primeiro login
        $user->primeiro_login = 1;
        $user->save(false);

        $this->loginPaciente($I);

        // Notificação obrigatória
        $I->see('Ok, preencher agora');
        $I->click('Ok, preencher agora');

        // Página de edição do perfil
        $I->see('Editar Perfil');

        $I->fillField('UserProfile[nif]', '999999991');
        $I->fillField('UserProfile[sns]', '999999991');
        $I->fillField('UserProfile[telefone]', '912345678');
        $I->fillField('UserProfile[datanascimento]', '1995-05-10');
        $I->selectOption('UserProfile[genero]', 'M');
        $I->fillField('UserProfile[morada]', 'Rua de Testes');

        $I->click('Submeter Formulário');

        // Agora já pode aceder à triagem
        $I->seeLink('Triagem');
    }

    /* =====================================================
     * TESTE 3 — Preencher formulário clínico e gerar pulseira
     * ===================================================== */
    public function pacientePreencheFormularioTriagem(FunctionalTester $I)
    {
        $this->loginPaciente($I);

        // Aceder à triagem
        $I->click('Triagem');
        $I->see('Preencher Formulário Clínico');
        $I->click('Preencher Formulário Clínico');

        $I->see('Formulário Clínico');

        // Preencher formulário
        $I->fillField('Triagem[motivoconsulta]', 'Dor abdominal');
        $I->fillField('Triagem[queixaprincipal]', 'Dor intensa no abdómen');
        $I->fillField('Triagem[descricaosintomas]', 'Dores persistentes há dois dias');

        $I->fillField(
            '#triagem-iniciosintomas',
            date('Y-m-d\TH:i', strtotime('-1 day'))
        );

        $I->selectOption('Triagem[intensidadedor]', '8');
        $I->fillField('Triagem[alergias]', 'Nenhuma');
        $I->fillField('Triagem[medicacao]', 'Paracetamol');

        $I->click('Submeter Formulário');

        // Resultado esperado
        $I->see('O seu número de triagem');
        $I->see('PENDENTE');
    }

    /**
     * TESTE A
     * Perfil incompleto + primeiro login
     * → deve mostrar notificação obrigatória
     */
    public function pacientePrimeiroLoginComPerfilIncompletoMostraAviso(FunctionalTester $I)
    {
        /* ===============================
         * Garantir utilizador
         * =============================== */
        User::deleteAll(['username' => 'paciente_test']);

        $user = new User();
        $user->username = 'paciente_test';
        $user->email = 'paciente_test@example.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->primeiro_login = 1;
        $user->save(false);

        // Role paciente
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('paciente');
        $auth->assign($role, $user->id);

        /* ===============================
         * Perfil incompleto (irrelevante agora)
         * =============================== */
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->nome = 'Paciente Teste';
        $profile->email = $user->email;
        $profile->save(false);

        /* ===============================
         * Login
         * =============================== */
        $I->amOnRoute('site/login');
        $I->fillField('LoginForm[username]', 'paciente_test');
        $I->fillField('LoginForm[password]', 'password123');
        $I->click('Entrar');

        /* ===============================
         * 🔑 SIMULAR PRIMEIRO LOGIN (SESSION)
         * =============================== */
        $I->executeInYii(function () {
            Yii::$app->session->set('firstLogin', true);
        });

        // Voltar à homepage para disparar o site/index
        $I->amOnRoute('site/index');

        /* ===============================
         * Verificar notificação
         * =============================== */
        $I->see('Ok, preencher agora');
        $I->click('Ok, preencher agora');

        $I->see('Editar Perfil');
    }

    /**
     * TESTE B
     * Perfil completo + primeiro login
     * → NÃO deve mostrar notificação
     */
    public function pacientePrimeiroLoginComPerfilCompletoNaoMostraAviso(FunctionalTester $I)
    {
        /* ===============================
         * Garantir utilizador
         * =============================== */
        User::deleteAll(['username' => 'paciente_test']);

        $user = new User();
        $user->username = 'paciente_test';
        $user->email = 'paciente_test@example.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->primeiro_login = 1;
        $user->save(false);

        // Role paciente
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('paciente');
        $auth->assign($role, $user->id);

        /* ===============================
         * Perfil COMPLETO
         * =============================== */
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->nome = 'Paciente Teste';
        $profile->email = $user->email;
        $profile->nif = '999999991';
        $profile->sns = '999999991';
        $profile->telefone = '912345678';
        $profile->datanascimento = '1995-05-10';
        $profile->genero = 'M';
        $profile->morada = 'Rua de Testes';
        $profile->save(false);

        /* ===============================
         * Login
         * =============================== */
        $I->amOnRoute('site/login');
        $I->fillField('LoginForm[username]', 'paciente_test');
        $I->fillField('LoginForm[password]', 'password123');
        $I->click('Entrar');

        /* ===============================
         * Verificações
         * =============================== */

        // NÃO deve aparecer notificação
        $I->dontSee('Ok, preencher agora');

        // Deve poder navegar normalmente
        $I->seeLink('Triagem');
    }

    /* =====================================================
     * MÉTODOS AUXILIARES (reutilizáveis)
     * ===================================================== */

    private function criarPaciente()
    {
        // Evitar duplicados
        User::deleteAll(['username' => 'paciente_test']);

        $user = new User();
        $user->username = 'paciente_test';
        $user->email = 'paciente_test@example.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->primeiro_login = true;
        $user->save(false);

        // Role paciente
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('paciente');
        $auth->assign($role, $user->id);

        // Perfil mínimo
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->nome = 'Paciente Teste';
        $profile->email = $user->email;
        $profile->save(false);
    }

    private function loginPaciente(FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
        $I->fillField('LoginForm[username]', 'paciente_test');
        $I->fillField('LoginForm[password]', 'password123');
        $I->click('Entrar');
    }
    private function garantirPacienteExiste()
    {
        $user = User::findOne(['username' => 'paciente_test']);

        if (!$user) {
            $user = new User();
            $user->username = 'paciente_test';
            $user->email = 'paciente_test@example.com';
            $user->setPassword('password123');
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE;
            $user->primeiro_login = 1;
            $user->save(false);

            // Role paciente
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('paciente');
            $auth->assign($role, $user->id);

            // Perfil mínimo
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->nome = 'Paciente Teste';
            $profile->email = $user->email;
            $profile->save(false);
        }

        return $user;
    }
}
