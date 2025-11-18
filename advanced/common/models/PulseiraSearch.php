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
            ->joinWith(['userprofile', 'triagem t', 'triagem.consulta c']); // 🔥 não removi nada!

        // 🔥🔥🔥 AQUI ESTÁ O FILTRO QUE FALTAVA
        // Mostra apenas pulseiras que NÃO têm consulta associada
        $query->andWhere(['c.id' => null]); // ← alias correto da consulta

        // DataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // 🔹 Ordenação personalizada Manchester
        $dataProvider->sort->attributes['prioridade'] = [
            'asc' => [new Expression("FIELD(pulseira.prioridade, 'Azul', 'Verde', 'Amarelo', 'Laranja', 'Vermelho') ASC")],
            'desc' => [new Expression("FIELD(pulseira.prioridade, 'Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul') ASC")],
        ];

        // 🔹 Ordenação padrão (mais recentes primeiro)
        $dataProvider->setSort([
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // === Filtros ===
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'prioridade', $this->prioridade])
            ->andFilterWhere(['like', 'status', $this->status]);

        if (!empty($this->tempoentrada)) {
            $query->andFilterWhere(['like', 'tempoentrada', $this->tempoentrada]);
        }

        // (❗ este filtro era duplicado com o de cima, mas mantive o teu comentário)
        // ❗ Ocultar pulseiras que já têm consulta
        // ❗ Já está tratado pelo filtro c.id IS NULL

        return $dataProvider;
    }
}
