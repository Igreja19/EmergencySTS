<?php
namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class PacienteSearch extends Paciente
{
    public $q; // pesquisa global opcional

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nomecompleto','nif','telefone','email','morada','genero','sns','q'], 'safe'],
            [['datanascimento'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Paciente::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id','nomecompleto','nif','datanascimento','genero','telefone','email']
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Filtros específicos
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['genero' => $this->genero]);

        if (!empty($this->datanascimento)) {
            // permite filtrar por YYYY-MM-DD
            $query->andFilterWhere(['datanascimento' => $this->datanascimento]);
        }

        $query
            ->andFilterWhere(['like', 'nomecompleto', $this->nomecompleto])
            ->andFilterWhere(['like', 'nif', $this->nif])
            ->andFilterWhere(['like', 'telefone', $this->telefone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'morada', $this->morada])
            ->andFilterWhere(['like', 'sns', $this->sns]);

        // Pesquisa global opcional (?q=texto)
        if ($this->q) {
            $query->andWhere([
                'or',
                ['like', 'nomecompleto', $this->q],
                ['like', 'nif', $this->q],
                ['like', 'telefone', $this->q],
                ['like', 'email', $this->q],
                ['like', 'morada', $this->q],
            ]);
        }

        return $dataProvider;
    }
}
