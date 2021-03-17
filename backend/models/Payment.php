<?php
/**
 * Created by PhpStorm.
 * User: alina
 * Date: 13.01.19
 * Time: 23:03
 */

namespace frontend\models;

use yii\db\ActiveRecord;

/**
 * Class Payment
 * @package frontend\models
 */
class Payment extends ActiveRecord
{
    /**
     * Нужно для отчета
     * @var string name
     */
    public $name;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['lead_id', 'shop_id'], 'integer'],
            [['amount'], 'double'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::class, ['id' => 'shop_id']);
    }
}


















