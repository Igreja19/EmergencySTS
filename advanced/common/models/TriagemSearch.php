<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class TriagemSearch extends Triagem
{
    public function rules()
    {
        return [
            [['id', 'intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [
                [
                    'motivoconsulta',
                    'queixaprincipal',
                    'descricaosintomas',
                    'iniciosintomas',
                    'alergias',
                    'medicacao',
                    'datatriagem'
                ],
                'safe'
            ],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Triagem::find()
            ->joinWith(['userprofile', 'pulseira']);

        // 🔥 Mostrar apenas triagens cuja pulseira está PENDENTE
        $query->andWhere(['pulseira.prioridade' => 'Pendente']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'defaultOrder' => ['datatriagem' => SORT_DESC]
            ],
        ]);

        // Ordenação por prioridade (opcional)
        $dataProvider->sort->attributes['prioridade'] = [
            'asc' => [
                new Expression("FIELD(pulseira.prioridade, 'Azul','Verde','Amarelo','Laranja','Vermelho')")
            ],
            'desc' => [
                new Expression("FIELD(pulseira.prioridade, 'Vermelho','Laranja','Amarelo','Verde','Azul')")
            ],
        ];

        // Ordem padrão: mais recente primeiro
        $dataProvider->sort->defaultOrder = ['datatriagem' => SORT_DESC];

        // Carregar filtros do formulário
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filtros exatos
        $query->andFilterWhere([
            'triagem.id'       => $this->id,
            'intensidadedor'   => $this->intensidadedor,
            'userprofile_id'   => $this->userprofile_id,
            'pulseira_id'      => $this->pulseira_id,
        ]);

        // Filtros LIKE
        $query->andFilterWhere(['like', 'motivoconsulta', $this->motivoconsulta])
            ->andFilterWhere(['like', 'queixaprincipal', $this->queixaprincipal])
            ->andFilterWhere(['like', 'descricaosintomas', $this->descricaosintomas])
            ->andFilterWhere(['like', 'alergias', $this->alergias])
            ->andFilterWhere(['like', 'medicacao', $this->medicacao]);

        // Filtro por data
        if (!empty($this->datatriagem)) {
            $inicio = $this->datatriagem . ' 00:00:00';
            $fim    = $this->datatriagem . ' 23:59:59';
            $query->andFilterWhere(['between', 'datatriagem', $inicio, $fim]);
        }

        return $dataProvider;
    }
}
