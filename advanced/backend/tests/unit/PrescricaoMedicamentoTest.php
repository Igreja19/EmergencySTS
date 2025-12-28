<?php

namespace backend\tests\unit;

use common\models\PrescricaoMedicamento;
use common\models\Prescricao;
use common\models\Medicamento;

class PrescricaoMedicamentoTest extends \Codeception\Test\Unit
{
    public function testPrescricaoMedicamentoValidacaoInvalida()
    {
        $pm = new PrescricaoMedicamento();

        $pm->prescricao_id = null;
        $pm->medicamento_id = null;
        $pm->posologia = '';

        $this->assertFalse(
            $pm->validate(),
            json_encode($pm->errors)
        );

        $this->assertArrayHasKey('prescricao_id', $pm->errors);
        $this->assertArrayHasKey('medicamento_id', $pm->errors);
        $this->assertArrayHasKey('posologia', $pm->errors);
    }

    public function testPrescricaoMedicamentoValidacaoValida()
    {
        $prescricao = Prescricao::find()->one();
        $medicamento = Medicamento::find()->one();

        $this->assertNotNull($prescricao, 'É necessária uma Prescrição na BD');
        $this->assertNotNull($medicamento, 'É necessário um Medicamento na BD');

        $pm = new PrescricaoMedicamento();
        $pm->prescricao_id = $prescricao->id;
        $pm->medicamento_id = $medicamento->id;
        $pm->posologia = '1 comprimido de 8 em 8 horas';

        $this->assertTrue(
            $pm->validate(),
            json_encode($pm->errors)
        );
    }

    public function testPrescricaoMedicamentoCicloDeVidaNaBD()
    {
        $prescricao = Prescricao::find()->one();
        $medicamento = Medicamento::find()->one();

        $this->assertNotNull($prescricao);
        $this->assertNotNull($medicamento);

        // Criar
        $pm = new PrescricaoMedicamento();
        $pm->prescricao_id = $prescricao->id;
        $pm->medicamento_id = $medicamento->id;
        $pm->posologia = '1 comprimido por dia';

        $this->assertTrue(
            $pm->save(),
            json_encode($pm->errors)
        );

        // Ler
        $pmBD = PrescricaoMedicamento::findOne($pm->id);
        $this->assertNotNull($pmBD);

        // Update
        $pmBD->posologia = '2 comprimidos por dia';
        $this->assertTrue($pmBD->save());

        // Apagar
        $id = $pmBD->id;
        $pmBD->delete();

        $this->assertNull(PrescricaoMedicamento::findOne($id));
    }
}
