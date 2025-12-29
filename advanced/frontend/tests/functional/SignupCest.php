<?php

namespace frontend\tests\functional;

use common\models\User;
use frontend\tests\FunctionalTester;

class SignupCest
{
    protected $formId = '#form-signup';


    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/signup');
    }

    public function signupWithEmptyFields(FunctionalTester $I)
    {
        $I->amOnRoute('site/signup');
        $I->seeInTitle('Criar Conta');
        $I->click('Criar Conta');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function signupWithWrongEmail(FunctionalTester $I)
    {
        $I->submitForm(
            $this->formId, [
            'SignupForm[username]'  => 'tester',
            'SignupForm[email]'     => 'ttttt',
            'SignupForm[password]'  => 'tester_password',
        ]
        );
        $I->dontSee('Username cannot be blank.', '.invalid-feedback');
        $I->dontSee('Password cannot be blank.', '.invalid-feedback');
        $I->see('Email is not a valid email address.', '.invalid-feedback');
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $I->amOnRoute('site/signup');
        $I->seeInTitle('Criar Conta');

        $username = 'tester_' . time();
        $email = $username . '@example.com';

        $I->submitForm('#form-signup', [
            'SignupForm[username]' => $username,
            'SignupForm[email]' => $email,
            'SignupForm[password]' => 'tester_password',
        ]);

        $I->seeRecord(User::class, [
            'username' => $username,
            'email' => $email,
        ]);
    }
}
