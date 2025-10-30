<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserProfileSearch representa o modelo de pesquisa para `common\models\Userprofile`.
 */
class UserProfileSearch extends Userprofile
{
    public $q; // campo de pesquisa global

    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['nome', 'email', 'morada', 'nif', 'sns', 'datanascimento', 'genero', 'telefone', 'q'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Userprofile::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 10],
        ]);

        // 🔹 Carrega parâmetros
        $this->load($params);

        // 🔹 Se o campo 'q' vier do formulário GET manual, atribui-o manualmente
        if (isset($params['UserProfileSearch']['q'])) {
            $this->q = $params['UserProfileSearch']['q'];
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 🔍 Pesquisa global (campo 'q')
        if (!empty($this->q)) {
            $query->andFilterWhere(['or',
                ['like', 'nome', $this->q],
                ['like', 'email', $this->q],
                ['like', 'telefone', $this->q],
                ['like', 'nif', $this->q],
                ['like', 'sns', $this->q],
                ['like', 'morada', $this->q],
            ]);
        }

        // 🔹 Filtros adicionais (opcional)
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'genero', $this->genero]);
        $query->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
