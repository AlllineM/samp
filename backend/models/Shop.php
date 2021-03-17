<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Shop extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['town_id', 'amo_id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['shop_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTown()
    {
        return $this->hasOne(Town::class, ['id' => 'town_id']);
    }
}