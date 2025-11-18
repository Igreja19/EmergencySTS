<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Pulseira;
use yii\db\Expression;

/**
 * PulseiraSearch representa o modelo de pesquisa para `common\models\Pulseira`.
 */
class PulseiraSearch extends Pulseira
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['codigo', 'prioridade', 'tempoentrada', 'status'], 'safe'],
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
     * Cria um DataProvider com a query de pesquisa aplicada.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // Query base
        $query = Pulseira::find()
            ->joinWith(['userprofile', 'triagem t', 'triagem.consulta c']);

        // 🔥 1) Mostrar apenas pulseiras SEM consulta associada (mantido do teu código)
        $query->andWhere(['c.id' => null]);

        // 🔥 2) Mostrar apenas pulseiras que JÁ TÊM PRIORIDADE REAL (ocultar PENDENTE)
        $query->andWhere(['!=', 'pulseira.prioridade', 'Pendente']);

        // DataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // 🔹 Ordenação personalizada Manchester
        $dataProvider->sort->attributes['prioridade'] = [
            'asc' => [
                new Expression("FIELD(pulseira.prioridade, 'Azul', 'Verde', 'Amarelo', 'Laranja', 'Vermelho')")
            ],
            'desc' => [
                new Expression("FIELD(pulseira.prioridade, 'Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul')")
            ],
        ];

        // 🔹 Ordenação padrão (mais recentes primeiro)
        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        // ← carregar filtros
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // === Filtros adicionais ===
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'prioridade', $this->prioridade])
            ->andFilterWhere(['like', 'status', $this->status]);

        if (!empty($this->tempoentrada)) {
            $query->andFilterWhere(['like', 'tempoentrada', $this->tempoentrada]);
        }

        return $dataProvider;
    }
}
