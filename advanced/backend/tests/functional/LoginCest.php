<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use common\models\User;
use Yii;

/**
 * Class LoginCest
 */
class LoginCest
{
    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }
    
    /**
     * @param FunctionalTester $I
     */
    public function loginUser(FunctionalTester $I)
    {
        // Criar user de teste
        $user = new User();
        $user->username = 'admin_test';
        $user->email = 'admin_test@example.com';
        $user->setPassword('admin123');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('admin'); // ou medico / enfermeiro
        $auth->assign($role, $user->id);

        $I->amOnRoute('site/login');
        $I->fillField('LoginForm[username]', 'admin_test');
        $I->fillField('LoginForm[password]', 'admin123');
        $I->click('Iniciar Sessão');

        // Verificar sucesso
        $I->see('Sair');
    }
}
