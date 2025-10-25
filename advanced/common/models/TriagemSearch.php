<?php

namespace common\models;

use backend\models\Triagem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TriagemSearch represents the model behind the search form of `backend\models\Triagem`.
 */
class TriagemSearch extends Triagem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'intensidadedor', 'paciente_id', 'utilizador_id'], 'integer'],
            [['nomecompleto', 'datanascimento', 'sns', 'telefone', 'motivoconsulta', 'queixaprincipal', 'descricaosintomas', 'iniciosintomas', 'condicoes', 'alergias', 'medicacao', 'motivo', 'prioridadeatribuida', 'datatriagem', 'discriminacaoprincipal'], 'safe'],
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
        $query = Triagem::find();

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
            'datanascimento' => $this->datanascimento,
            'iniciosintomas' => $this->iniciosintomas,
            'intensidadedor' => $this->intensidadedor,
            'datatriagem' => $this->datatriagem,
            'paciente_id' => $this->paciente_id,
            'utilizador_id' => $this->utilizador_id,
        ]);

        $query->andFilterWhere(['like', 'nomecompleto', $this->nomecompleto])
            ->andFilterWhere(['like', 'sns', $this->sns])
            ->andFilterWhere(['like', 'telefone', $this->telefone])
            ->andFilterWhere(['like', 'motivoconsulta', $this->motivoconsulta])
            ->andFilterWhere(['like', 'queixaprincipal', $this->queixaprincipal])
            ->andFilterWhere(['like', 'descricaosintomas', $this->descricaosintomas])
            ->andFilterWhere(['like', 'condicoes', $this->condicoes])
            ->andFilterWhere(['like', 'alergias', $this->alergias])
            ->andFilterWhere(['like', 'medicacao', $this->medicacao])
            ->andFilterWhere(['like', 'motivo', $this->motivo])
            ->andFilterWhere(['like', 'prioridadeatribuida', $this->prioridadeatribuida])
            ->andFilterWhere(['like', 'discriminacaoprincipal', $this->discriminacaoprincipal]);

        return $dataProvider;
    }
}
