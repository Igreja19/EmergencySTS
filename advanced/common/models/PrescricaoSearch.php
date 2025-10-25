<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Prescricao;

/**
 * PrescricaoSearch represents the model behind the search form of `common\models\Prescricao`.
 */
class PrescricaoSearch extends Prescricao
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'consulta_id'], 'integer'],
            [['medicamento', 'dosagem', 'frequencia', 'observacoes', 'dataprescricao'], 'safe'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Prescricao::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'dataprescricao' => $this->dataprescricao,
            'consulta_id' => $this->consulta_id,
        ]);

        $query->andFilterWhere(['like', 'medicamento', $this->medicamento])
            ->andFilterWhere(['like', 'dosagem', $this->dosagem])
            ->andFilterWhere(['like', 'frequencia', $this->frequencia])
            ->andFilterWhere(['like', 'observacoes', $this->observacoes]);

        return $dataProvider;
    }
}
