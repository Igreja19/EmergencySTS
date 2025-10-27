<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Notificacao extends ActiveRecord
{
    const TIPO_CONSULTA = 'Consulta';
    const TIPO_PRIORIDADE = 'Prioridade';
    const TIPO_GERAL = 'Geral';

    public static function tableName()
    {
        return 'notificacao';
    }

    // 🔹 Contadores (KPI)
    public static function countNaoLidas()
    {
        return self::find()->where(['lida' => 0])->count();
    }

    public static function countHoje()
    {
        return self::find()
            ->where(['>=', 'dataenvio', date('Y-m-d 00:00:00')])
            ->count();
    }

    public static function countTotal()
    {
        return self::find()->count();
    }
}
