<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use Yii;

/* @var $scenario \Codeception\Scenario */

class ContactCest
{
    public function _before(FunctionalTester $I)
    {

    }

    public function checkContact(FunctionalTester $I)
    {
        $output = Yii::$app->runAction('site/contact');

        $I->assertNotEmpty($output);
        $I->assertStringContainsString('Contacta-nos', $output);
    }

    public function checkContactSubmitNoData(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];

        $output = Yii::$app->runAction('site/contact');

        $I->assertStringContainsString('O campo Nome é obrigatório', $output);
        $I->assertStringContainsString('O campo Email é obrigatório', $output);
        $I->assertStringContainsString('O campo Assunto é obrigatório', $output);
        $I->assertStringContainsString('O campo Mensagem é obrigatório', $output);
    }

    public function checkContactSubmitNotCorrectEmail(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'ContactForm' => [
                'name' => 'tester',
                'email' => 'tester.email',
                'subject' => 'assunto',
                'body' => 'mensagem',
            ]
        ];

        $output = Yii::$app->runAction('site/contact');

        $I->assertStringContainsString(
            'Por favor, insira um endereço de email válido',
            $output
        );
    }

    public function checkContactSubmitCorrectData(FunctionalTester $I)
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'ContactForm' => [
                'name' => 'tester',
                'email' => 'tester@example.com',
                'subject' => 'assunto',
                'body' => 'mensagem',
            ]
        ];

        Yii::$app->request->setUrl('/site/contact');

        Yii::$app->runAction('site/contact');

        $I->seeEmailIsSent();
    }
}
