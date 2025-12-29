<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

class HomeCest
{
    public function checkOpen(FunctionalTester $I)
    {
        $I->amOnRoute('site/index');
        $I->seeInTitle('EmergencySTS');
        $I->see('Início');
        $I->seeLink('Sobre');
        $I->click('Sobre');
        $I->seeInTitle('Sobre');
    }
}