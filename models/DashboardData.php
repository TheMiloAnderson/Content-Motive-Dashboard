<?php

namespace app\models;

use Yii;
use app\models\gii\GoogleAnalyticsAggregates;
use app\models\gii\GoogleAnalyticsDetails;
use app\models\gii\GoogleAnalyticsProperties;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

class DashboardData {
    
    public function aggregates($pids) {
        if (!$this->userAuth($pids)) {
            return NULL;
        } else {
            $data = GoogleAnalyticsAggregates::find()
                ->where(['in', 'property_id', $pids])
                ->joinWith('property', true, 'INNER JOIN')
                ->orderBy(['date_recorded' => SORT_ASC])
                ->asArray()
                ->all();
            foreach ($data as &$datum) {
                $datum['url'] = $datum['property']['url'];
                $datum['dealer_id'] = $datum['property']['dealer_id'];
                unset($datum['property']);
            }
            return $data;
        }
    }
    public function details($pids, $start, $end) {
        if (!$this->userAuth($pids)) {
            return NULL;
        } else {
            $pagesize = 10;
            $sort = [
                'attributes' => [
                    'pageviews' => ['default' => SORT_DESC],
                    'visitors' => ['default' => SORT_DESC],
                    'entrances' => ['default' => SORT_DESC],
                    'avg_time' => ['default' => SORT_DESC],
                    'bounce_rate' => ['default' => SORT_DESC],
                ],
                'defaultOrder' => [
                    'entrances' => SORT_DESC,
                ],
            ];
            if ($start == null && $end == null) {
                $query  = GoogleAnalyticsDetails::find()
                    ->where(['in', 'property_id', $pids]);
                $provider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => $pagesize,
                    ],
                    'sort' => $sort,
                ]);
            } else {
                $totalCount = Yii::$app->db->createCommand('SELECT COUNT(*) FROM 
                    (SELECT page
                    FROM ga_analytics
                    WHERE property_id IN (:pids)
                    AND date_recorded BETWEEN :start AND :end
                    GROUP BY page) as c')
                    ->bindValue(':pids', implode(',', $pids))
                    ->bindValue(':start', $start)
                    ->bindValue(':end', $end)
                    ->queryScalar();
                $query = 'SELECT
                    property_id,
                    page,
                    SUM(pageviews) as pageviews,
                    SUM(visitors) as visitors,
                    SUM(entrances) as entrances,
                    AVG(avg_time) as avg_time,
                    IFNULL(SUM(bounce_rate * entrances)/SUM(entrances), 0) / 100 AS bounce_rate
                FROM ga_analytics
                WHERE property_id IN (:pids)
                    AND date_recorded BETWEEN :start AND :end
                GROUP BY page';
                $provider = new SqlDataProvider([
                    'sql' => $query,
                    'pagination' => [
                        'pageSize' => $pagesize,
                    ],
                    'sort' => $sort,
                    'totalCount' => $totalCount,
                    'params' => [
                        ':pids' => implode(',', $pids),
                        ':start' => $start,
                        ':end' => $end,
                    ],
                ]);
            }
            $html = GridView::widget([
                'dataProvider' => $provider,
                'layout'=>"{items}{pager}",
                'tableOptions' => ['class' => 'table table-bordered'],
                'columns' => [
                    ['attribute' => 'page',
                        'label' => $this->createSummary($provider),
                        'encodeLabel' => false,
                        'value' => function($data) {return $this->formatPage($data);},
                        'contentOptions' => ['class' => 'content-strategy']],
                    ['attribute' => 'entrances',
                        'label' => '',
                        'format' => 'integer',
                        'contentOptions' => ['class' => 'entrances']],
                    ['attribute' => 'visitors',
                        'label' => '',
                        'format' => 'integer',
                        'contentOptions' => ['class' => 'visitors']],
                    ['attribute' => 'pageviews',
                        'label' => '',
                        'format' => 'integer',
                        'contentOptions' => ['class' => 'pageviews']],
                    ['attribute' => 'avg_time',
                        'label' => '',
                        'value' => function($data) {return $this->formatTime($data);},
                        'contentOptions' => ['class' => 'avg_time']],
                    ['attribute' => 'bounce_rate',
                        'label' => '',
                        'format' => 'percent',
                        'contentOptions' => ['class' => 'bounce_rate']],
                ]
            ]);
            return $html;
        }
    }
    private function userAuth($pids) {
        $currentUserId = Yii::$app->user->id;
        $currentUser = Users::find()->where(['id' => $currentUserId])->one();
        $user_dealers = $currentUser->getDealers()->asArray()->all();
        $user_dealer_ids = ArrayHelper::getColumn($user_dealers, 'id');
        $user_properties = GoogleAnalyticsProperties::find()
            ->where(['dealer_id' => $user_dealer_ids])->asArray()->all();
        $user_property_ids = ArrayHelper::getColumn($user_properties, 'id');
        $return = TRUE;
        foreach ($pids as $pid) {
            if (!ArrayHelper::isIn($pid, $user_property_ids)) {
                $return = FALSE;
            }
        }
        return $return;
    }
    private function createSummary($dataProvider) {
        $dataProvider->prepare();
        $paginationObj = $dataProvider->getPagination();
        $rangeStart = (($paginationObj->pageSize * $paginationObj->page) + 1);
        $rangeEnd = ($paginationObj->pageSize * ($paginationObj->page + 1));
        $total = $paginationObj->totalCount;
        $summary = 'Showing ' . $rangeStart . '-';
        $summary .= $rangeEnd > $total ? $total : $rangeEnd;
        $summary .= ' of ' . $total . ' items.';
        return $summary;
    }
    private function formatTime($data) {
        if (is_object($data)) {
            $seconds = '0' . ($data->avg_time % 60);
            return (int)($data->avg_time / 60) . ':' . substr($seconds, -2);
        } else {
            $seconds = '0' . ($data['avg_time'] % 60);
            return (int)($data['avg_time'] / 60) . ':' . substr($seconds, -2);
        }
    }
    private function formatPage($data) {
        if (is_object($data)) {
            $page = $data->page;
        } else {
            $page = $data['page'];
        }
        $patterns = [
            '/\/op/',
            '/\-/',
            '/\//',
        ];
        $page = preg_replace($patterns, ' ', $page);
        return trim($page);
    }
}