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
        return $this->hasOne(\common\models\UserProfile::class, ['id' => 'userprofile_id']);
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

    /**
     *  Texto formatado da prioridade com ícone
     */
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
    /**
     * Envia notificação MQTT após criar uma nova pulseira
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Só envia se for uma INSERÇÃO (nova pulseira)
        if ($insert) {
            $topico = "pulseira/criada/" . $this->id;

            // Cria um JSON simples para o Android ler
            $payload = json_encode([
                'id' => $this->id,
                'codigo' => $this->codigo,
                'mensagem' => 'Nova pulseira registada: ' . $this->codigo
            ]);

            // Verifica se o componente MQTT está configurado e publica
            if (isset(Yii::$app->mqtt)) {
                try {
                    Yii::$app->mqtt->publish($topico, $payload);
                } catch (\Exception $e) {
                    // Log de erro silencioso para não parar o save
                    Yii::error("Erro MQTT ao criar pulseira: " . $e->getMessage());
                }
            }
        }
    }
}