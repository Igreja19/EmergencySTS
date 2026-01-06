<?php


namespace backend\tests\Unit;

use backend\tests\UnitTester;
use common\models\User;
use common\models\UserProfile;

class UtilizadorTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
    }

    public function testUserValidacaoInvalida()
    {
        $user = new User();

        // Campos inválidos
        $user->username = '';
        $user->email = 'email-sem-arroba';

        // Não definir password

        $this->assertFalse($user->validate());

        $this->assertArrayHasKey('username', $user->errors);
        $this->assertArrayHasKey('email', $user->errors);
    }

    public function testUserProfileValidacaoInvalida()
    {
        $profile = new UserProfile();

        // Campos inválidos
        $profile->nome = ' ';
        $profile->email = 'email-sem-arroba';
        $profile->morada = '';
        $profile->nif = '123'; // inválido
        $profile->sns = 'abc'; // inválido
        $profile->datanascimento = '2099-01-01'; // futuro
        $profile->genero = 'X'; // fora do enum
        $profile->telefone = '999'; // curto
        $profile->user_id = null; // obrigatório

        $this->assertFalse($profile->validate());

        $this->assertArrayHasKey('nome', $profile->errors);
        $this->assertArrayHasKey('email', $profile->errors);
        $this->assertArrayHasKey('morada', $profile->errors);
        $this->assertArrayHasKey('nif', $profile->errors);
        $this->assertArrayHasKey('sns', $profile->errors);
        $this->assertArrayHasKey('datanascimento', $profile->errors);
        $this->assertArrayHasKey('genero', $profile->errors);
        $this->assertArrayHasKey('telefone', $profile->errors);
        $this->assertArrayHasKey('user_id', $profile->errors);
    }

    public function testCicloDeVidaNaBD()
    {
        $emailTeste = 'testeunit@emergencysts.com';
        $nifTeste   = '999999999';

        $userLixo = User::findOne(['email' => $emailTeste]);
        if ($userLixo) {
            UserProfile::deleteAll(['user_id' => $userLixo->id]);
            $userLixo->delete();
        }

        $user = new User();
        $user->username = 'teste_unit';
        $user->email = $emailTeste;
        $user->setPassword('123456');
        $user->status = User::STATUS_ACTIVE;

        $this->assertTrue(
            $user->save(),
            'Falha ao guardar User: ' . json_encode($user->getErrors())
        );

        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->nome = 'Ana Teste';
        $profile->email = $emailTeste;
        $profile->nif = $nifTeste;
        $profile->sns = '123456789';
        $profile->datanascimento = '1999-01-01';
        $profile->genero = 'M';
        $profile->telefone = '912345678';
        $profile->morada = 'Rua do Codeception, 10';

        $this->assertTrue(
            $profile->save(),
            'Falha ao guardar UserProfile: ' . json_encode($profile->getErrors())
        );

        $profileBD = UserProfile::findOne(['nif' => $nifTeste]);
        $this->assertNotNull($profileBD);
        $this->assertEquals('Ana Teste', $profileBD->nome);

        $profileBD->nome = 'Ana Atualizada';
        $this->assertTrue($profileBD->save());

        $profileAtualizado = UserProfile::findOne([
            'nif' => $nifTeste,
            'nome' => 'Ana Atualizada'
        ]);
        $this->assertNotNull($profileAtualizado);

        $profileAtualizado->delete();
        $user->delete();

        $this->assertNull(UserProfile::findOne(['nif' => $nifTeste]));
        $this->assertNull(User::findOne(['email' => $emailTeste]));
    }
}
