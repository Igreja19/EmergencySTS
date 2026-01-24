<?php

namespace common\models;

use Yii;

/**
 * Esta é a classe modelo para a tabela "pulseira".
 *
 * @property int $id
 * @property string $codigo
 * @property string $prioridade
 * @property string|null $status
 * @property string $tempoentrada
 * @property int $userprofile_id
 *
 * @property UserProfile $userprofile
 * @property Triagem $triagem
 */
class Pulseira extends \yii\db\ActiveRecord
{
    public $triagem_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pulseira';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'prioridade', 'tempoentrada', 'userprofile_id'], 'required'],
            [['tempoentrada'], 'safe'],
            [['userprofile_id'], 'integer'],
            [['prioridade'], 'in', 'range' => ['Pendente','Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul']],
            [['status'], 'in', 'range' => ['Em espera', 'Em atendimento', 'Atendido', 'Finalizado']],
            [['codigo'], 'string', 'max' => 10],
            [['codigo'], 'unique'],
            [['userprofile_id'], 'exist', 'skipOnError' => true,
                'targetClass' => UserProfile::class, 'targetAttribute' => ['userprofile_id' => 'id']],
            [['triagem_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código da Pulseira',
            'prioridade' => 'Prioridade',
            'status' => 'Estado',
            'tempoentrada' => 'Tempo de Entrada',
            'userprofile_id' => 'Utilizador',
        ];
    }

    /**
     *  Relação com o perfil do utilizador
     */
    public function getUserprofile()
    {
        return $this->hasOne(UserProfile::class, ['id' => 'userprofile_id']);
    }

    public function getPaciente()
    {
        return $this->getUserprofile();
    }

    /**
     *  Relação com a triagem (uma triagem cria uma pulseira)
     */
    public function getTriagem()
    {
        return $this->hasOne(Triagem::class, ['pulseira_id' => 'id']);
    }

    public function getPrioridadeComCor()
    {
        $cores = [
            'Pendente' => '⚪ Pendente - A aguardar triagem',
            'Vermelho' => '🔴 Vermelho - Emergente',
            'Laranja'  => '🟠 Laranja - Muito Urgente',
            'Amarelo'  => '🟡 Amarelo - Urgente',
            'Verde'    => '🟢 Verde - Pouco Urgente',
            'Azul'     => '🔵 Azul - Não Urgente',
        ];
        return $cores[$this->prioridade] ?? $this->prioridade;
    }
    public function beforeSave($insert)
    {
        if ($insert) {
            // Guarda automaticamente o timestamp atual
            $this->tempoentrada = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {

            if ($this->status === null) {
                $this->status = 'Em espera';
            }

            if (empty($this->codigo)) {
                $this->codigo = strtoupper(substr(md5(uniqid()), 0, 8));
            }

            if (empty($this->tempoentrada)) {
                $this->tempoentrada = date('Y-m-d H:i:s');
            }
        }

        return parent::beforeValidate();
    }

    public function extraFields()
    {
        // Permite usar ?expand=triagem,paciente
        return ['triagem', 'paciente', 'userprofile'];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!isset(Yii::$app->mqtt)) {
            return;
        }

        try {
            $payloadPaciente = [
                'id' => $this->id,
                'codigo' => $this->codigo,
                'titulo' => $insert ? 'Nova Pulseira' : 'Atualização',
                'mensagem' => $insert
                    ? 'A sua pulseira foi registada com sucesso. Aguarde a triagem.'
                    : "O seu estado foi atualizado para {$this->prioridade} ({$this->status})",
                'prioridade' => $this->prioridade,
                'status' => $this->status,
                'tipo' => 'paciente'
            ];

            $topicoPaciente = "notificacao/paciente/{$this->userprofile_id}";
            Yii::$app->mqtt->publish($topicoPaciente, json_encode($payloadPaciente));

            $payloadEnfermeiro = [
                'id' => $this->id,
                'codigo' => $this->codigo,
                'titulo' => $insert ? 'Nova Pulseira Criada' : 'Pulseira Atualizada',
                'mensagem' => $insert
                    ? "Nova pulseira criada: {$this->codigo}"
                    : "Pulseira {$this->codigo} atualizada para {$this->prioridade} ({$this->status})",
                'prioridade' => $this->prioridade,
                'status' => $this->status,
                'tipo' => 'enfermeiro'
            ];

            $topicoEnfermeiro = "notificacao/enfermeiro";
            Yii::$app->mqtt->publish($topicoEnfermeiro, json_encode($payloadEnfermeiro));

        } catch (\Exception $e) {
            Yii::error("Erro MQTT: " . $e->getMessage());
        }
    }
}