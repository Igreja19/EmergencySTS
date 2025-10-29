<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Triagem;

/**
 * TriagemSearch represents the model behind the search form of `common\models\Triagem`.
 */
class TriagemSearch extends Triagem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [['motivoconsulta', 'queixaprincipal', 'descricaosintomas', 'iniciosintomas', 'alergias', 'medicacao', 'motivo', 'datatriagem'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Triagem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'iniciosintomas' => $this->iniciosintomas,
            'intensidadedor' => $this->intensidadedor,
            'datatriagem' => $this->datatriagem,
            'userprofile_id' => $this->userprofile_id,
            'pulseira_id' => $this->pulseira_id,
        ]);

        $query->andFilterWhere(['like', 'motivoconsulta', $this->motivoconsulta])
            ->andFilterWhere(['like', 'queixaprincipal', $this->queixaprincipal])
            ->andFilterWhere(['like', 'descricaosintomas', $this->descricaosintomas])
            ->andFilterWhere(['like', 'alergias', $this->alergias])
            ->andFilterWhere(['like', 'medicacao', $this->medicacao])
            ->andFilterWhere(['like', 'motivo', $this->motivo]);

        return $dataProvider;
    }
}
