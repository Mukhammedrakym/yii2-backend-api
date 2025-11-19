<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m251119_110745_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title'        => $this->string()->notNull(),
            'author'       => $this->string()->notNull(),
            'published_at' => $this->date()->null(),
            'description'  => $this->text()->null(),
            'owner_id'     => $this->integer()->null(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-book-owner', '{{%book}}', 'owner_id');
        $this->addForeignKey('fk-book-owner', '{{%book}}', 'owner_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-book-owner', '{{%book}}');
        $this->dropIndex('idx-book-owner', '{{%book}}');
        $this->dropTable('{{%book}}');
    }
}
