<?php


namespace backend\tests\Unit;

use common\models\Pulseira;
use common\models\Triagem;
use common\models\UserProfile;

class TriagemTest extends \Codeception\Test\Unit
{

    public function testTriagemValidacaoInvalida()
    {
        $triagem = new Triagem();

        // Falta userprofile_id
        $triagem->userprofile_id = null;

        $this->assertFalse($triagem->validate(), json_encode($triagem->errors));
        $this->assertArrayHasKey('userprofile_id', $triagem->errors);
    }

    public function testTriagemValidacaoValida()
    {
        $profile = UserProfile::find()->one();
        $this->assertNotNull($profile, 'É necessário existir um UserProfile');

        $triagem = new Triagem();
        $triagem->userprofile_id = $profile->id;

        $this->assertTrue($triagem->validate(), json_encode($triagem->errors));
    }

    public function testTriagemCicloDeVidaNaBD()
    {
        $profile = UserProfile::find()->one();
        $this->assertNotNull($profile);

        // Criar
        $triagem = new Triagem();
        $triagem->userprofile_id = $profile->id;

        $this->assertTrue(
            $triagem->save(),
            'Erro ao guardar Triagem: ' . json_encode($triagem->errors)
        );

        // Verificar default
        $this->assertNotNull($triagem->datatriagem);

        // Ler
        $triagemBD = Triagem::findOne($triagem->id);
        $this->assertNotNull($triagemBD);

        // Update — preencher dados clínicos
        $triagemBD->motivoconsulta = 'Dor abdominal';
        $triagemBD->queixaprincipal = 'Dor intensa na zona inferior do abdómen';
        $triagemBD->intensidadedor = 8;
        $triagemBD->alergias = 'Nenhuma';
        $triagemBD->medicacao = 'Paracetamol';

        $this->assertTrue($triagemBD->save(), json_encode($triagemBD->errors));

        // Confirmar update
        $this->assertEquals(8, $triagemBD->intensidadedor);
        $this->assertEquals('Dor abdominal', $triagemBD->motivoconsulta);

        // Associar Pulseira
        $pulseira = Pulseira::find()->one();
        if ($pulseira) {
            $triagemBD->pulseira_id = $pulseira->id;
            $this->assertTrue($triagemBD->save());
            $this->assertEquals($pulseira->id, $triagemBD->pulseira_id);
        }

        // Apagar
        $id = $triagemBD->id;
        $triagemBD->delete();

        $this->assertNull(Triagem::findOne($id));
    }
}
