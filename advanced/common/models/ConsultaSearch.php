<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Consulta;

/**
 * ConsultaSearch represents the model behind the search form of `common\models\Consulta`.
 */
class ConsultaSearch extends Consulta
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'userprofile_id', 'triagem_id', 'prescricao_id'], 'integer'],
            [['data_consulta', 'estado', 'prioridade', 'motivo', 'observacoes', 'data_encerramento', 'tempo_consulta', 'relatorio_pdf'], 'safe'],
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
        $query = Consulta::find();

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
            'data_consulta' => $this->data_consulta,
            'userprofile_id' => $this->userprofile_id,
            'triagem_id' => $this->triagem_id,
            'prescricao_id' => $this->prescricao_id,
            'data_encerramento' => $this->data_encerramento,
        ]);

        $query->andFilterWhere(['like', 'estado', $this->estado])
            ->andFilterWhere(['like', 'prioridade', $this->prioridade])
            ->andFilterWhere(['like', 'motivo', $this->motivo])
            ->andFilterWhere(['like', 'observacoes', $this->observacoes])
            ->andFilterWhere(['like', 'tempo_consulta', $this->tempo_consulta])
            ->andFilterWhere(['like', 'relatorio_pdf', $this->relatorio_pdf]);

        return $dataProvider;
    }
}
