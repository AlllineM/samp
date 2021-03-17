<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%shop}}`
 */
class m200210_173531_create_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'lead_id' => $this->integer(),
            'date' => $this->date(),
            'amount' => $this->money(),
            'shop_id' => $this->integer(),
        ]);

        // creates index for column `amount`
        $this->createIndex(
            '{{%idx-payment-amount}}',
            '{{%payment}}',
            'amount'
        );

        // add foreign key for table `{{%shop}}`
        $this->addForeignKey(
            '{{%fk-payment-shop_id}}',
            '{{%payment}}',
            'shop_id',
            '{{%shop}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%shop}}`
        $this->dropForeignKey(
            '{{%fk-payment-amount}}',
            '{{%payment}}'
        );

        // drops index for column `amount`
        $this->dropIndex(
            '{{%idx-payment-amount}}',
            '{{%payment}}'
        );

        $this->dropTable('{{%payment}}');
    }
}
