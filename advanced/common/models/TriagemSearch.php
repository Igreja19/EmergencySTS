<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Triagem;

/**
 * TriagemSearch representa o modelo de pesquisa para `common\models\Triagem`.
 */
class TriagemSearch extends Triagem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'intensidadedor', 'userprofile_id', 'pulseira_id'], 'integer'],
            [['motivoconsulta', 'queixaprincipal', 'descricaosintomas', 'iniciosintomas', 'alergias', 'medicacao', 'datatriagem'], 'safe'],
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
     * Cria um DataProvider com a query de pesquisa aplicada.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Triagem::find()->joinWith(['userprofile', 'pulseira']);

        // DataProvider padrão
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Ordenação padrão (opcional)
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
            'intensidadedor' => $this->intensidadedor,
            'userprofile_id' => $this->userprofile_id,
            'pulseira_id' => $this->pulseira_id,
        ]);

        // Filtros textuais
        $query->andFilterWhere(['like', 'motivoconsulta', $this->motivoconsulta])
            ->andFilterWhere(['like', 'queixaprincipal', $this->queixaprincipal])
            ->andFilterWhere(['like', 'descricaosintomas', $this->descricaosintomas])
            ->andFilterWhere(['like', 'alergias', $this->alergias])
            ->andFilterWhere(['like', 'medicacao', $this->medicacao]);

        // Filtro por data
        if (!empty($this->datatriagem)) {
            $query->andFilterWhere(['like', 'datatriagem', $this->datatriagem]);
        }

        return $dataProvider;
    }
}
