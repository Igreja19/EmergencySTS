<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ConsultaSearch extends Consulta
{
    public function rules()
    {
        return [
            [['id', 'userprofile_id', 'triagem_id'], 'integer'],
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
            ->joinWith(['userprofile', 'triagem'], false);

        $query->andWhere(['<>', 'consulta.estado', 'Encerrada']); //não mostra consultas encerradas


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

        $query->andFilterWhere([
            'id' => $this->id,
            'userprofile_id' => $this->userprofile_id,
            'triagem_id' => $this->triagem_id,
            'data_encerramento' => $this->data_encerramento,
        ]);
        if (!empty($this->data_consulta)) {

            $data = \DateTime::createFromFormat('Y-m-d', $this->data_consulta);

            if ($data) {

                $inicio = $data->format('Y-m-d 00:00:00');
                $fim    = $data->format('Y-m-d 23:59:59');

                $query->andFilterWhere([
                    'between',
                    'consulta.data_consulta',
                    $inicio,
                    $fim
                ]);
            }
        }


        $query->andFilterWhere(['like', 'consulta.estado', $this->estado])
            ->andFilterWhere(['like', 'observacoes', $this->observacoes])
            ->andFilterWhere(['like', 'relatorio_pdf', $this->relatorio_pdf]);

        return $dataProvider;
    }
}
