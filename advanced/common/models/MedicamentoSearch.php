<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Medicamento;

class MedicamentoSearch extends Medicamento
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [
                [
                    'nome',
                    'dosagem',
                    'indicacao',
                    'quantidade_diaria',
                    'duracao_tratamento'
                ],
                'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Cria o data provider com a pesquisa aplicada
     */
    public function search($params, $formName = null)
    {
        $query = Medicamento::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 🔍 FILTRO DE PESQUISA MODERNO
        $query->andFilterWhere(['id' => $this->id]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'dosagem', $this->dosagem])
            ->andFilterWhere(['like', 'indicacao', $this->indicacao])
            ->andFilterWhere(['like', 'quantidade_diaria', $this->quantidade_diaria])
            ->andFilterWhere(['like', 'duracao_tratamento', $this->duracao_tratamento]);

        return $dataProvider;
    }
}
