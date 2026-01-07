<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use backend\modules\api\mqtt\phpMQTT; // O namespace do ficheiro que enviaste

class MqttService extends Component
{
    public $server;
    public $port;
    public $clientId;
    public $username = null;
    public $password = null;

    public function publish($topic, $payload)
    {
        // 1. Cria a instância usando a classe que enviaste
        $mqtt = new phpMQTT($this->server, $this->port, $this->clientId);

        // 2. Tenta conectar
        // A assinatura deste método é: connect(clean, will, username, password)
        if ($mqtt->connect(true, null, $this->username, $this->password)) {

            // 3. Publica a mensagem (QoS 0, Retain false)
            $mqtt->publish($topic, $payload, 0, false);

            // 4. Fecha a conexão para não bloquear o PHP
            $mqtt->close();

            return true;
        } else {
            Yii::error("MQTT: Falha ao conectar ao broker {$this->server}");
            return false;
        }
    }
}