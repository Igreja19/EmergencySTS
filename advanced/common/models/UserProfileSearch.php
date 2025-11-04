<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserProfile;

class UserProfileSearch extends UserProfile
{
    public $q;           // 🔍 Campo de pesquisa geral
    public $created_at;  // 📅 Filtro de data de registo

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome', 'email', 'telefone', 'genero', 'datanascimento', 'created_at', 'q'], 'safe'],
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
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 🔍 Filtro geral (pesquisa em múltiplos campos)
        if (!empty($this->q)) {
            $query->andFilterWhere([
                'or',
                ['like', 'nome', $this->q],
                ['like', 'email', $this->q],
                ['like', 'telefone', $this->q],
                ['like', 'nif', $this->q],
            ]);
        }

        // 📅 Filtro por data (formato do input type=date)
        if (!empty($this->created_at)) {
            $query->andWhere(['DATE(created_at)' => $this->created_at]);
        }

        // Ordenação padrão
        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        return $dataProvider;
    }
}
