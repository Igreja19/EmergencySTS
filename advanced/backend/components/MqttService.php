<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use backend\modules\api\mqtt\phpMQTT;

class MqttService extends Component
{
    public $server = '127.0.0.1';
    public $port = 1883;
    public $clientId = 'backend-' . 1234;

    public function publish($topic, $payload)
    {
        $mqtt = new phpMQTT($this->server, $this->port, $this->clientId);

        if (!$mqtt->connect(true, NULL)) {
            Yii::error("MQTT falhou conectar ao broker");
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();

        return true;
    }
}
