<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Prescricao;

class PrescricaoSearch extends Prescricao
{
    public $medicamento; // Campo virtual para pesquisa pelo nome do medicamento

    public function rules()
    {
        return [
            [['id', 'consulta_id'], 'integer'],
            [['observacoes', 'dataprescricao', 'medicamento'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Prescricao::find();

        // Faz o join com as tabelas relacionadas
        $query->joinWith(['prescricaomedicamentos.medicamento']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Permite ordenar pela coluna medicamento.nome
        $dataProvider->sort->attributes['medicamento'] = [
            'asc' => ['medicamento.nome' => SORT_ASC],
            'desc' => ['medicamento.nome' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'prescricao.id' => $this->id,
            'prescricao.consulta_id' => $this->consulta_id,
        ]);

        $query->andFilterWhere(['like', 'prescricao.observacoes', $this->observacoes])
            ->andFilterWhere(['like', 'medicamento.nome', $this->medicamento]);

        return $dataProvider;
    }
}
