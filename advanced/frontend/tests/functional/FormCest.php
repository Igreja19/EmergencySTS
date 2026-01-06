<?php

namespace frontend\tests\functional;

use common\models\Pulseira;
use common\models\Triagem;
use common\models\User;
use common\models\UserProfile;
use frontend\tests\FunctionalTester;
use Yii;

class FormCest
{

    protected FunctionalTester $tester;

    protected function _before()
    {
    }

    public function pacienteConsegueFazerLogin(FunctionalTester $I)
    {
        $this->criarPaciente();

        $this->criarPaciente();

        $user = User::findOne(['username' => 'paciente_test']);
        Yii::$app->user->login($user);

        $I->assertFalse(Yii::$app->user->isGuest);
        $I->assertEquals('paciente_test', Yii::$app->user->identity->username);
    }

    public function pacienteCompletaPerfilNoPrimeiroLogin(FunctionalTester $I)
    {
        $user = $this->garantirPacienteExiste();

        $user->primeiro_login = 1;
        $user->save(false);

        Yii::$app->user->login($user);

        Yii::$app->session->set('firstLogin', true);

        Yii::$app->runAction('site/index');

        $I->assertNull(Yii::$app->session->get('firstLogin'));

        $profile = $user->userprofile;
        $profile->nif = '999999991';
        $profile->sns = '999999991';
        $profile->telefone = '912345678';
        $profile->datanascimento = '1995-05-10';
        $profile->genero = 'M';
        $profile->morada = 'Rua de Testes';
        $profile->save(false);

        $I->assertTrue($profile->validate());
    }

    public function pacientePreencheFormularioTriagem(FunctionalTester $I)
    {
        $user = $this->garantirPacienteExiste();
        Yii::$app->user->login($user);

        $profile = $user->userprofile;
        $profile->nif = '999999991';
        $profile->sns = '999999991';
        $profile->telefone = '912345678';
        $profile->datanascimento = '1995-05-10';
        $profile->genero = 'M';
        $profile->morada = 'Rua de Testes';
        $profile->save(false);

        Yii::$app->request->setBodyParams([
            'Triagem' => [
                'motivoconsulta'    => 'Dor abdominal',
                'queixaprincipal'   => 'Dor intensa no abdómen',
                'descricaosintomas' => 'Dores persistentes há dois dias',
                'iniciosintomas'    => date('Y-m-d H:i:s', strtotime('-1 day')),
                'intensidadedor'    => 8,
                'alergias'          => 'Nenhuma',
                'medicacao'         => 'Paracetamol',
            ]
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';

        Yii::$app->runAction('triagem/formulario');

        // Triagem criada
        $triagem = Triagem::find()
            ->where(['userprofile_id' => $profile->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $I->assertNotNull($triagem, 'A triagem deveria ter sido criada');
        $I->assertEquals('Dor abdominal', $triagem->motivoconsulta);

        // Pulseira criada
        $pulseira = Pulseira::find()
            ->where(['userprofile_id' => $profile->id])
            ->one();

        $I->assertNotNull($pulseira, 'A pulseira deveria ter sido criada');
        $I->assertEquals('Pendente', $pulseira->prioridade);
    }




    public function pacientePrimeiroLoginComPerfilIncompletoMostraAviso(FunctionalTester $I)
    {
        User::deleteAll(['username' => 'paciente_test']);

        $user = new User();
        $user->username = 'paciente_test';
        $user->email = 'paciente_test@example.com';
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->primeiro_login = 1;
        $user->save(false);

        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole('paciente'), $user->id);

        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->nome = 'Paciente Teste';
        $profile->email = $user->email;
        $profile->save(false);

        Yii::$app->user->login($user);

        Yii::$app->session->set('firstLogin', true);

        Yii::$app->runAction('site/index');

        $I->assertNull(
            Yii::$app->session->get('firstLogin'),
            'First Login muda para 0'
        );

        $I->assertFalse(Yii::$app->user->isGuest);
    }

    public function pacientePrimeiroLoginComPerfilCompletoNaoMostraAviso(FunctionalTester $I)
    {
        $user = $this->garantirPacienteExiste();

        $user->primeiro_login = 1;
        $user->save(false);

        // Perfil completo
        $profile = $user->userprofile;
        $profile->nif = '999999991';
        $profile->sns = '999999991';
        $profile->telefone = '912345678';
        $profile->datanascimento = '1995-05-10';
        $profile->genero = 'M';
        $profile->morada = 'Rua de Testes';
        $profile->save(false);

        Yii::$app->user->login($user);

        Yii::$app->runAction('site/index');

        // Não existe firstLogin
        $I->assertNull(Yii::$app->session->get('firstLogin'));
    }


     // MÉTODOS AUXILIARES

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
