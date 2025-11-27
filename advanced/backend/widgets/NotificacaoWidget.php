<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;
use common\models\Notificacao;

class NotificacaoWidget extends Widget
{
    public function run()
    {
        $user = Yii::$app->user->identity;

        if (!$user || !$user->userprofile) {
            return '';
        }

        $userId = $user->userprofile->id;

        $naoLidas = Notificacao::find()
            ->where(['userprofile_id' => $userId, 'lida' => 0])
            ->orderBy(['dataenvio' => SORT_DESC])
            ->limit(5)
            ->all();

        $totalNaoLidas = Notificacao::find()
            ->where(['userprofile_id' => $userId, 'lida' => 0])
            ->count();

        return $this->render('notificacao', [
            'naoLidas' => $naoLidas,
            'totalNaoLidas' => $totalNaoLidas
        ]);
    }
}
