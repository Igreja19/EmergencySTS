<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ModelHelper
{
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model = new $modelClass;
        $formName = $model->formName();
        $post = Yii::$app->request->post($formName);

        $models = [];

        // Se não há POST, devolve o modelo inicial
        if (empty($post)) {
            return $multipleModels;
        }

        // Reindexa corretamente modelos existentes
        if (!empty($multipleModels)) {
            $existingIDs = ArrayHelper::map($multipleModels, 'id', 'id');
        } else {
            $existingIDs = [];
        }

        foreach ($post as $i => $item) {
            if (isset($item['id']) && isset($existingIDs[$item['id']])) {
                // modelo existente
                $models[] = $multipleModels[array_search($item['id'], $existingIDs)];
            } else {
                // novo modelo
                $models[] = new $modelClass;
            }
        }

        return $models;
    }

    public static function loadMultiple($models, $data)
    {
        return Model::loadMultiple($models, $data);
    }
}
