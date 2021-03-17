<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%shop}}`.
 */
class m200221_111054_add_amo_id_column_to_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop}}', 'amo_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%shop}}', 'amo_id');
    }
}
