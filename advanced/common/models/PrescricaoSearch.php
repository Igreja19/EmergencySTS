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
            [['observacoes', 'dataprescricao'], 'safe'],
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
            'consulta_id' => $this->consulta_id,
        ]);

        if (!empty($this->dataprescricao)) {

            $data = \DateTime::createFromFormat('Y-m-d', $this->dataprescricao);

            if ($data) {

                $inicio = $data->format('Y-m-d 00:00:00');
                $fim    = $data->format('Y-m-d 23:59:59');

                $query->andFilterWhere([
                    'between',
                    'dataprescricao',
                    $inicio,
                    $fim
                ]);
            }
        }

        return $dataProvider;
    }
}
