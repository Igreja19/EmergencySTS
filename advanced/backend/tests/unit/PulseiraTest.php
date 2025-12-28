<?php


namespace backend\tests\Unit;

use backend\tests\UnitTester;
use common\models\Pulseira;
use common\models\UserProfile;

class PulseiraTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
    }

    public function testPulseiraValidacaoInvalida()
    {
        $pulseira = new Pulseira();

        // Dados inválidos
        $pulseira->prioridade = 'Roxo';
        $pulseira->userprofile_id = null;

        $this->assertFalse($pulseira->validate(), json_encode($pulseira->errors));

        $this->assertArrayHasKey('prioridade', $pulseira->errors);
        $this->assertArrayHasKey('userprofile_id', $pulseira->errors);
    }

    public function testPulseiraValidacaoValida()
    {
        $profile = UserProfile::find()->one();
        $this->assertNotNull($profile, 'É necessário existir um UserProfile');

        $pulseira = new Pulseira();
        $pulseira->userprofile_id = $profile->id;
        $pulseira->prioridade = 'Verde';

        $this->assertTrue($pulseira->validate(), json_encode($pulseira->errors));
    }

    public function testPulseiraCicloDeVidaNaBD()
    {
        $profile = UserProfile::find()->one();
        $this->assertNotNull($profile);

        // Criar
        $pulseira = new Pulseira();
        $pulseira->userprofile_id = $profile->id;
        $pulseira->prioridade = 'Amarelo';

        $this->assertTrue(
            $pulseira->save(),
            'Erro ao guardar Pulseira: ' . json_encode($pulseira->errors)
        );

        // Verificar defaults
        $this->assertNotNull($pulseira->codigo);
        $this->assertEquals('Em espera', $pulseira->status);
        $this->assertNotNull($pulseira->tempoentrada);

        // Ler
        $pulseiraBD = Pulseira::findOne($pulseira->id);
        $this->assertNotNull($pulseiraBD);

        // Update
        $pulseiraBD->status = 'Em atendimento';
        $this->assertTrue($pulseiraBD->save());

        // Confirmar update
        $this->assertEquals('Em atendimento', $pulseiraBD->status);

        // Apagar
        $id = $pulseiraBD->id;
        $pulseiraBD->delete();

        $this->assertNull(Pulseira::findOne($id));
    }
}
