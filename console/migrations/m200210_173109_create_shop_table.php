<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%town}}`
 */
class m200210_173109_create_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop}}', [
            'id' => $this->primaryKey(),
            'town_id' => $this->integer()->notNull(),
            'name' => $this->string(),
        ]);

        // creates index for column `town_id`
        $this->createIndex(
            '{{%idx-shop-town_id}}',
            '{{%shop}}',
            'town_id'
        );

        // add foreign key for table `{{%town}}`
        $this->addForeignKey(
            '{{%fk-shop-town_id}}',
            '{{%shop}}',
            'town_id',
            '{{%town}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%town}}`
        $this->dropForeignKey(
            '{{%fk-shop-town_id}}',
            '{{%shop}}'
        );

        // drops index for column `town_id`
        $this->dropIndex(
            '{{%idx-shop-town_id}}',
            '{{%shop}}'
        );

        $this->dropTable('{{%shop}}');
    }
}
