<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ApiLog;

/**
 * ApiLogSearch represents the model behind the search form about `app\models\ApiLog`.
 */
class ApiLogSearch extends ApiLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'api_method_id', 'api_log_description_id', 'user_id'], 'integer'],
            [['type', 'notes', 'created', 'device_ip_address', 'trans_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ApiLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'api_method_id' => $this->api_method_id,
            'api_log_description_id' => $this->api_log_description_id,
            'user_id' => $this->user_id,
            'created' => $this->created,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'device_ip_address', $this->device_ip_address])
            ->andFilterWhere(['like', 'trans_id', $this->trans_id]);

        return $dataProvider;
    }
}
