<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ModelHelper
{
    /**
     * Cria múltiplos modelos, garantindo que não há índices fora do array
     */
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model = new $modelClass;
        $formName = $model->formName();

        $post = Yii::$app->request->post($formName, []);

        $models = [];

        // 🔥 Reindexar o POST — SEMPRE
        $post = array_values($post);

        // Para actualización
        if (!empty($multipleModels)) {
            $multipleModels = array_values($multipleModels); // 🔥 Reindexa modelos antigos

            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id'])) {

                    // procurar modelo existente com esse ID
                    foreach ($multipleModels as $old) {
                        if ($old->id == $item['id']) {
                            $models[$i] = $old;
                            break;
                        }
                    }

                    // Se não encontrar um modelo correspondente → cria novo
                    if (!isset($models[$i])) {
                        $models[$i] = new $modelClass;
                    }
                } else {
                    // Novo item (sem ID)
                    $models[$i] = new $modelClass;
                }
            }

        } else {
            // CREATE — só novos modelos
            foreach ($post as $i => $item) {
                $models[$i] = new $modelClass;
            }
        }

        return $models;
    }

    /**
     * Carrega vários modelos
     */
    public static function loadMultiple(&$models, $data)
    {
        $firstModel = reset($models);
        $formName = $firstModel->formName();

        if (!isset($data[$formName])) {
            return false;
        }

        foreach ($models as $i => $model) {
            if (isset($data[$formName][$i])) {
                $model->load([$formName => $data[$formName][$i]]);
            }
        }

        return true;
    }

}
