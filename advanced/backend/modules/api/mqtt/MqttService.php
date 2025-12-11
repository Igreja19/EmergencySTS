<?php

namespace backend\modules\api\mqtt;

require_once __DIR__ . '/phpMQTT.php';

class MqttService
{
    private string $server = '127.0.0.1';
    private int $port = 1883;
    private string $clientPrefix = 'emergencysts-api-';

    public function publish(string $topic, string $payload): bool
    {
        $clientId = $this->clientPrefix . rand(1000, 9999);

        $mqtt = new phpMQTT($this->server, $this->port, $clientId);

        if (!$mqtt->connect(true, null)) {
            return false;
        }

        $mqtt->publish($topic, $payload, 0);
        $mqtt->close();
        return true;
    }
}
