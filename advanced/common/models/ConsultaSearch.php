<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ConsultaSearch extends Consulta
{
    public function rules()
    {
        return [
            [['id', 'userprofile_id', 'triagem_id', 'prescricao_id'], 'integer'],
            [['data_consulta', 'estado', 'observacoes', 'data_encerramento', 'relatorio_pdf'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Consulta::find()
            ->joinWith(['userprofile', 'triagem', 'prescricao']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filtros exatos
        $query->andFilterWhere([
            'id' => $this->id,
            'data_consulta' => $this->data_consulta,
            'userprofile_id' => $this->userprofile_id,
            'triagem_id' => $this->triagem_id,
            'prescricao_id' => $this->prescricao_id,
            'data_encerramento' => $this->data_encerramento,
        ]);

        // Filtros textuais
        $query->andFilterWhere(['like', 'estado', $this->estado])
            ->andFilterWhere(['like', 'observacoes', $this->observacoes])
            ->andFilterWhere(['like', 'relatorio_pdf', $this->relatorio_pdf]);

        return $dataProvider;
    }
}
