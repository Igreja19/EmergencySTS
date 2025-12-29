<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

/* @var $scenario \Codeception\Scenario */

class ContactCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/contact');
    }

    public function checkContact(FunctionalTester $I)
    {
        $I->see('Contacta-nos');
    }

    public function checkContactSubmitNoData(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', []);
        $I->see('Contacta-nos');
        $I->seeValidationError('O campo Nome é obrigatório.');
        $I->seeValidationError('O campo Email é obrigatório');
        $I->seeValidationError('O campo Assunto é obrigatório.');
        $I->seeValidationError('O campo Mensagem é obrigatório.');
    }

    public function checkContactSubmitNotCorrectEmail(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester.email',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
        ]);
        $I->seeValidationError('Por favor, insira um endereço de email válido.');
    }

    public function checkContactSubmitCorrectData(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester@example.com',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
        ]);
        $I->seeEmailIsSent();
    }
}
