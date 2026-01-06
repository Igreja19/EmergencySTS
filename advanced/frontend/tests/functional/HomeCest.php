<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use Yii;

class HomeCest
{
    public function checkHome(FunctionalTester $I)
    {
        Yii::$app->request->setUrl('/site/index');

        $output = Yii::$app->runAction('site/index');

        $I->assertNotEmpty($output);
        $I->assertStringContainsString(
            'EmergencySTS',
            $output,
            'O título EmergencySTS deve aparecer'
        );

        $I->assertStringContainsString(
            'Início',
            $output,
            'A página inicial deve ter a palavra Início'
        );
    }
}
