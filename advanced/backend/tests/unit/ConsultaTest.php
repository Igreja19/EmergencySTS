<?php


namespace backend\tests\Unit;

use common\models\Consulta;
use common\models\UserProfile;
use common\models\Triagem;
use backend\tests\UnitTester;

class ConsultaTest extends \Codeception\Test\Unit
{
    public function testConsultaValidacaoInvalida()
    {
        $consulta = new Consulta();

        // Faltam FKs obrigatórias
        $consulta->userprofile_id = null;
        $consulta->triagem_id = null;
        $consulta->medicouserprofile_id = null;
        $consulta->estado = 'X';

        $this->assertFalse($consulta->validate(), json_encode($consulta->errors));

        $this->assertArrayHasKey('userprofile_id', $consulta->errors);
        $this->assertArrayHasKey('triagem_id', $consulta->errors);
        $this->assertArrayHasKey('medicouserprofile_id', $consulta->errors);
        $this->assertArrayHasKey('estado', $consulta->errors);
    }

    public function testConsultaValidacaoValida()
    {
        $paciente = UserProfile::find()->one();
        $this->assertNotNull($paciente);

        $triagem = Triagem::find()->one();
        $this->assertNotNull($triagem);

        $medico = UserProfile::find()->one(); // idealmente role médico
        $this->assertNotNull($medico);

        $consulta = new Consulta();
        $consulta->userprofile_id = $paciente->id;
        $consulta->triagem_id = $triagem->id;
        $consulta->medicouserprofile_id = $medico->id;

        $this->assertTrue($consulta->validate(), json_encode($consulta->errors));
    }

    public function testConsultaCicloDeVidaNaBD()
    {
        $paciente = UserProfile::find()->one();
        $this->assertNotNull($paciente);

        $triagem = Triagem::find()->one();
        $this->assertNotNull($triagem);

        $medico = UserProfile::find()->one();
        $this->assertNotNull($medico);

        // Criar
        $consulta = new Consulta();
        $consulta->userprofile_id = $paciente->id;
        $consulta->triagem_id = $triagem->id;
        $consulta->medicouserprofile_id = $medico->id;

        $this->assertTrue(
            $consulta->save(),
            'Erro ao guardar Consulta: ' . json_encode($consulta->errors)
        );

        // Defaults
        $this->assertNotNull($consulta->data_consulta);
        $this->assertEquals(Consulta::ESTADO_EM_CURSO, $consulta->estado);

        // Ler
        $consultaBD = Consulta::findOne($consulta->id);
        $this->assertNotNull($consultaBD);

        // Update — Em curso
        $consultaBD->estado = 'Em curso';
        $this->assertTrue($consultaBD->save());
        $this->assertEquals('Em curso', $consultaBD->estado);

        // Encerrar
        $consultaBD->estado = 'Encerrada';
        $consultaBD->data_encerramento = date('Y-m-d H:i:s');
        $this->assertTrue($consultaBD->save());

        $this->assertNotNull($consultaBD->data_encerramento);

        // Observações / relatório
        $consultaBD->observacoes = 'Consulta concluída com sucesso';
        $consultaBD->relatorio_pdf = 'relatorio_123.pdf';
        $this->assertTrue($consultaBD->save());

        // Apagar
        $id = $consultaBD->id;
        $consultaBD->delete();

        $this->assertNull(Consulta::findOne($id));
    }
}
