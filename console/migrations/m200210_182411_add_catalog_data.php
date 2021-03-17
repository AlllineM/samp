<?php

use yii\db\Migration;

/**
 * Class m200210_182411_add_catalog_data
 */
class m200210_182411_add_catalog_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert('town', ['name'], [
            ['Чувашия'],
            ['Казань'],
            ['Нижний Новгород'],
        ])->execute();

        //это не очень хорошо, но тк миграции, то терпимо
        $lastId = Yii::$app->db->getLastInsertID();

        Yii::$app->db->createCommand()->insert('shop', [
            'town_id' => $lastId,
            'name' => 'NNV / Дверной центр',
        ])->execute();

        $lastId--;

        Yii::$app->db->createCommand()
            ->batchInsert('shop',
                ['town_id', 'name'],
                [
                    [$lastId, 'Казань / Дверной центр'],
                    [$lastId, 'Казань / ТЦ Савиново'],
                    [$lastId, 'Казань / ТЦ Новинка'],
                ])
            ->execute();

        $lastId--;

        Yii::$app->db->createCommand()
            ->batchInsert('shop',
                ['town_id', 'name'],
                [
                    [$lastId, 'Чебоксары / Дверной центр'],
                    [$lastId, 'Чебоксары / Гагарина, 41'],
                    [$lastId, 'Новочебоксарс / Чемурша'],
                ])
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()->delete('town')->execute();
        Yii::$app->db->createCommand()->delete('shop')->execute();
    }
}
