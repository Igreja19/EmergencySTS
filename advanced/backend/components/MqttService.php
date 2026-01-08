<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use backend\modules\api\mqtt\phpMQTT;

class MqttService extends Component
{
    public $server;
    public $port;
    public $clientId;

    public $username;
    public $password;

    public function publish($topic, $payload)
    {
        // Cria a instância usando as propriedades configuradas no main.php
        $mqtt = new phpMQTT($this->server, $this->port, $this->clientId);

        // Tenta conectar com autenticação
        if ($mqtt->connect(true, null, $this->username, $this->password)) {

            $mqtt->publish($topic, $payload, 0, false);
            $mqtt->close();

            return true;
        } else {
            Yii::error("MQTT Error: Falha ao conectar ao broker {$this->server} com o user {$this->username}");
            return false;
        }
    }
}