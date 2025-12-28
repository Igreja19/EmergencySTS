<?php

namespace backend\tests\unit;

use common\models\Prescricao;
use common\models\Consulta;

class PrescricaoTest extends \Codeception\Test\Unit
{
    public function testPrescricaoValidacaoInvalida()
    {
        $prescricao = new Prescricao();

        // Falta consulta_id (obrigatório)
        $prescricao->consulta_id = null;

        $this->assertFalse(
            $prescricao->validate(),
            json_encode($prescricao->errors)
        );

        $this->assertArrayHasKey('consulta_id', $prescricao->errors);
    }

    public function testPrescricaoValidacaoValida()
    {
        $consulta = Consulta::find()->one();
        $this->assertNotNull($consulta, 'É necessária uma Consulta na BD');

        $prescricao = new Prescricao();
        $prescricao->consulta_id = $consulta->id;
        $prescricao->observacoes = 'Tomar após as refeições';

        $this->assertTrue(
            $prescricao->validate(),
            json_encode($prescricao->errors)
        );
    }

    public function testPrescricaoCicloDeVidaNaBD()
    {
        $consulta = Consulta::find()->one();
        $this->assertNotNull($consulta, 'É necessária uma Consulta na BD');

        // Criar
        $prescricao = new Prescricao();
        $prescricao->consulta_id = $consulta->id;

        $this->assertTrue(
            $prescricao->save(),
            json_encode($prescricao->errors)
        );

        // Default da data
        $this->assertNotNull($prescricao->dataprescricao);

        // Ler
        $prescricaoBD = Prescricao::findOne($prescricao->id);
        $this->assertNotNull($prescricaoBD);

        // Update
        $prescricaoBD->observacoes = 'Alterar dose após 7 dias';
        $this->assertTrue($prescricaoBD->save());

        // Apagar
        $id = $prescricaoBD->id;
        $prescricaoBD->delete();

        $this->assertNull(Prescricao::findOne($id));
    }
}
