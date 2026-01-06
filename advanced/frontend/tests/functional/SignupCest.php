<?php

namespace frontend\tests\functional;

use common\models\User;
use frontend\tests\FunctionalTester;
use Yii;

class SignupCest
{
    public function _before(FunctionalTester $I)
    {

    }

    public function signupWithEmptyFields(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'SignupForm' => [
                'username' => '',
                'email'    => '',
                'password' => '',
            ]
        ];

        Yii::$app->request->setUrl('/site/signup');
        $output = Yii::$app->runAction('site/signup');

        $I->assertStringContainsString('Username cannot be blank', $output);
        $I->assertStringContainsString('Email cannot be blank', $output);
        $I->assertStringContainsString('Password cannot be blank', $output);

        $I->assertNull(User::findOne(['username' => '']));
    }

    public function signupWithWrongEmail(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'SignupForm' => [
                'username' => 'tester',
                'email'    => 'ttttt',
                'password' => 'tester_password',
            ]
        ];

        Yii::$app->request->setUrl('/site/signup');
        $output = Yii::$app->runAction('site/signup');

        $I->assertStringContainsString(
            'Email is not a valid email address',
            $output
        );

        $I->assertNull(User::findOne(['username' => 'tester']));
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $username = 'tester_' . time();
        $email = $username . '@example.com';

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'SignupForm' => [
                'username' => $username,
                'email'    => $email,
                'password' => 'password',
            ]
        ];

        Yii::$app->request->setUrl('/site/signup');
        Yii::$app->runAction('site/signup');

        $user = User::findOne(['username' => $username]);

        $I->assertNotNull($user, 'O utilizador foi criado');
        $I->assertEquals($email, $user->email);
    }
}
