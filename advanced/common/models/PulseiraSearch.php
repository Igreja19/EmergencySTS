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

    public function beforeValidate()
    {
        // Ignora os defaults da Pulseira
        return true;
    }

    /**
     * Cria um DataProvider com a query de pesquisa aplicada.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Pulseira::find()
            ->joinWith(['userprofile', 'triagem t', 'triagem.consulta c'], false)
            ->andWhere([
                'or',
                ['pulseira.status' => 'Em espera'],
                ['<>', 'c.estado', 'Encerrada'],
            ])
            ->andWhere(['!=', 'pulseira.prioridade', 'Pendente']);

        // DataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // 🔹 Ordenação personalizada Manchester (mantida)
        $dataProvider->sort->attributes['prioridade'] = [
            'asc' => [
                new Expression("FIELD(pulseira.prioridade, 'Azul', 'Verde', 'Amarelo', 'Laranja', 'Vermelho')")
            ],
            'desc' => [
                new Expression("FIELD(pulseira.prioridade, 'Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul')")
            ],
        ];

        // 🔹 Ordenação padrão
        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        // ← carregar filtros
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // === Filtros adicionais (mantidos, mas prefixados corretamente) ===
        $query->andFilterWhere([
            'pulseira.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'pulseira.codigo', $this->codigo])
            ->andFilterWhere(['like', 'pulseira.prioridade', $this->prioridade])
            ->andFilterWhere(['like', 'pulseira.status', $this->status]);

        // 🔥 Filtro de data CORRIGIDO (igual ao das consultas)
        if (!empty($this->tempoentrada)) {
            $query->andWhere([
                'between',
                'DATE(pulseira.tempoentrada)',
                $this->tempoentrada,
                $this->tempoentrada
            ]);
        }

        return $dataProvider;
    }
}
