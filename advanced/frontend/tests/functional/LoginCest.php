<?php

namespace frontend\tests\functional;

use common\models\User;
use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use Yii;

class LoginCest
{
    public function _before(FunctionalTester $I)
    {

    }

    public function checkEmptyLogin(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'LoginForm' => [
                'username' => '',
                'password' => '',
            ]
        ];

        Yii::$app->request->setUrl('/site/login');
        $output = Yii::$app->runAction('site/login');

        $I->assertTrue(Yii::$app->user->isGuest);

        $I->assertStringContainsString('Username cannot be blank', $output);
        $I->assertStringContainsString('Password cannot be blank', $output);
    }

    public function checkLoginWithWrongPassword(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'LoginForm' => [
                'username' => 'admin',
                'password' => 'password',
            ]
        ];

        Yii::$app->request->setUrl('/site/login');
        Yii::$app->runAction('site/login');

        $I->assertTrue(Yii::$app->user->isGuest);
    }

    public function checkInactiveAccount(FunctionalTester $I)
    {
        $user = User::findOne(['username' => 'test.test']);

        if (!$user) {
            $user = new User();
            $user->username = 'test.test';
            $user->email = 'test@test.com';
            $user->setPassword('test1234');
            $user->generateAuthKey();
            $user->status = User::STATUS_INACTIVE;
            $user->save(false);
        } else {
            $user->status = User::STATUS_INACTIVE;
            $user->save(false);
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'LoginForm' => [
                'username' => 'test.test',
                'password' => 'test1234',
            ]
        ];

        Yii::$app->request->setUrl('/site/login');
        $output = Yii::$app->runAction('site/login');

        $I->assertTrue(Yii::$app->user->isGuest);

        $I->assertStringContainsString('Incorrect username or password', $output);
    }

    public function checkValidLogin(FunctionalTester $I)
    {
        $user = User::findOne(['username' => 'admin']);

        if (!$user) {
            $user = new User();
            $user->username = 'admin';
            $user->email = 'admin@example.com';
            $user->setPassword('admin123');
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'LoginForm' => [
                'username' => 'admin',
                'password' => 'admin123',
            ]
        ];

        Yii::$app->request->setUrl('/site/login');
        Yii::$app->runAction('site/login');

        $I->assertFalse(Yii::$app->user->isGuest);
        $I->assertEquals('admin', Yii::$app->user->identity->username);
    }
}
