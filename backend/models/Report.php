<?php
namespace frontend\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

class Report extends Model
{
    public $shopId;
    public $amount;
    public $left;
    public $common;

    public $dateFrom;
    public $dateTo;
    public $year;

    public $trunc;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (\Yii::$app->amo->db == 'pgsql') {
            $this->trunc = ' DATE(DATE_TRUNC(\'month\', date)) ';
        } elseif (\Yii::$app->amo->db == 'mysql') {
            $this->trunc = ' EXTRACT(MONTH FROM date) ';
        }
    }

    public function formName()
    {
        return '';
    }



    /**
     * @param $leads
     * @param $amount
     * @return mixed
     */
    private function addAmoColumns($leads, $amount)
    {
        $shopsData = Shop::find()->asArray()->all();
        $shops = ArrayHelper::map($shopsData, 'amo_id', 'id');
        $towns = ArrayHelper::map($shopsData, 'amo_id', 'town_id');

        //Осторожно, адъ и содомия
        foreach ($amount as $key => $row) {
            foreach ($leads['_embedded']['items'] as $lead) {
                if (!isset($shops[$lead['responsible_user_id']])) continue;
                if (!isset($towns[$lead['responsible_user_id']])) continue;

                $leadShop = $shops[$lead['responsible_user_id']]; //id магазина, которому принадлежит сделка
                $leadTown = $towns[$lead['responsible_user_id']]; //id города, которому принаджет сделка

                //собираем бюджет сделок в $row['general'] в строчках городов

                if (!isset($row['shop_id'])) {      //если это строчка города
                    if ($row['town_id'] == $leadTown)
                        $amount[$key] = $this->makeAmoFields($row, $lead);
                } else { //если строчка магазина
                    if ($row['shop_id'] == $leadShop)
                        $amount[$key] = $this->makeAmoFields($row, $lead);
                }
            }
        }

        return $amount;
    }

    private function makeAmoFields($row, $lead)
    {
        //если пусто, кладем бюджет сделки. Иначе - плюсуем бюджет
        $row['general'] = !isset($row['general']) ? $lead['sale'] : $row['general'] + $lead['sale'];
        //аналогично остаток
        //$left = $lead['custom_fields'][2]['values'][0]['value'];
        //$row['waiting'] = !isset($row['waiting']) ? $left : $row['waiting'] + $left;

        return $row;
    }

    /**
     * Собираем данные для отчета за период(по умолчанию - текущий месяц)
     * @return ArrayDataProvider
     */
    public function monthReport($leads)
    {
        $shopQuery = Payment::find()
            ->select([
                'shop_id',
                'sum(amount) as amount',
                'shop.name as name',
                '(sum(town_id) / count(*)) as town_id']) //умеренно дикий костыль, а все из-за упоротой структуры таблицы. Надо бы переделать
            ->where(['>', 'date', $this->dateFrom])
            ->andWhere(['<', 'date', $this->dateTo])
            ->joinWith(['shop'])
            ->groupBy(['shop_id', 'shop.name'])
            ->createCommand()
            ->rawSql;

        $townQuery = Payment::find()
            ->select(['town_id', 'sum(amount) as amount', 'town.name as name'])
            ->where(['>', 'date', $this->dateFrom])
            ->andWhere(['<', 'date', $this->dateTo])
            ->joinWith('shop')
            ->join('LEFT JOIN', 'town', 'town_id = town.id')
            ->groupBy(['town_id', 'town.name'])
            ->createCommand()
            ->rawSql;

        $shopProvider = new SqlDataProvider(['sql' => $shopQuery,]);
        $townProvider = new SqlDataProvider(['sql' => $townQuery]);

        $amount = $this->sortUporotost($shopProvider->getModels(), $townProvider->getModels());
        $amount = $this->addAmoColumns($leads, $amount);

        $provider = new ArrayDataProvider([
            'allModels' => $amount,
        ]);


        return $provider;
    }

    /**
     * Собирает данные для отчетов по годам
     * @return ArrayDataProvider
     */
    public function yearReport($leads)
    {
        $shopQuery = Payment::find()
            ->select([
                'shop_id',
                'sum(amount) as amount',
                'shop.name as name',
                '(sum(town_id) / count(*)) as town_id',
                $this->trunc . 'as month'
            ])
            ->joinWith(['shop'])
            ->groupBy(['shop_id', 'shop.name', $this->trunc])
            ->createCommand()
            ->rawSql;

        $townQuery = Payment::find()
            ->select([
                'town_id',
                'sum(amount) as amount',
                'town.name as name',
                $this->trunc . 'as month'
            ])
            ->joinWith('shop')
            ->join('LEFT JOIN', 'town', 'town_id = town.id')
            ->groupBy(['town_id', 'town.name', $this->trunc])
            ->createCommand()
            ->rawSql;

        $shopProvider = new SqlDataProvider(['sql' => $shopQuery,]);
        $townProvider = new SqlDataProvider(['sql' => $townQuery]);

        $shops = $this->restructureData($shopProvider->getModels(), 'shop_id');
        $towns = $this->restructureData($townProvider->getModels(), 'town_id');

        $amount = $this->sortUporotost($shops, $towns);
        $amount = $this->addAmoColumns($leads, $amount);

        $provider = new ArrayDataProvider([
            'allModels' => $amount,
        ]);


        return $provider;
    }

    /**
     * Перегруппировывает данные для таблицы отчета по годам
     * @param $data
     * @param $key
     * @return array
     */
    private function restructureData($data, $key)
    {
        $temp = [];
        $result = [];

        //группируем по месяцам
        foreach ($data as $datum) {
            $temp[$datum[$key]][] = $datum;
        }
        //собираем данные по магазину за все месяцы в одину строку
        foreach ($temp as $group) {
            $newRow = [];
            foreach ($group as $item) {
                $monthNumber = date('n', strtotime($item['month'])) - 1;
                $newRow['amount_' . $monthNumber] = $item['amount'];
                $newRow['town_id'] = $item['town_id'];
                $newRow['name'] = $item['name'];
            }
            $result[] = $newRow;
        }

        return $result;
    }

    /**
     * Сортируем таблицы, чтобы привести их в упоротый вид, который нужен заказчику
     * @param $shops
     * @param $towns
     * @return array
     */
    private function sortUporotost($shops, $towns)
    {
        $data = [];
        foreach ($towns as $town) {
            $data[] = $town;
            foreach ($shops as $shop) {
                if ($shop['town_id'] == $town['town_id']) {
                    $data[] = $shop;
                }
            }
        }

        return $data;
    }
}