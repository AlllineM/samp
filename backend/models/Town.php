<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Town extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShops()
    {
        return $this->hasMany(Shop::class, ['town_id' => 'id']);
    }
}