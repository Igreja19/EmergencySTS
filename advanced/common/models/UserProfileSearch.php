<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserProfileSearch extends UserProfile
{
    public $q;

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
        $query = UserProfile::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Filtros individuais
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['user_id' => $this->user_id]);
        $query->andFilterWhere(['like', 'nome', $this->nome]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'morada', $this->morada]);
        $query->andFilterWhere(['like', 'nif', $this->nif]);
        $query->andFilterWhere(['like', 'sns', $this->sns]);
        $query->andFilterWhere(['like', 'genero', $this->genero]);
        $query->andFilterWhere(['like', 'telefone', $this->telefone]);

        // Pesquisa global
        if (!empty($this->q)) {
            $query->andFilterWhere(['or',
                ['like', 'nome', $this->q],
                ['like', 'email', $this->q],
                ['like', 'nif', $this->q],
                ['like', 'sns', $this->q],
                ['like', 'telefone', $this->q],
            ]);
        }

        return $dataProvider;
    }
}
