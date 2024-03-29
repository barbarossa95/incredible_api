<?php

use Phinx\Migration\AbstractMigration;

class UserInterestsTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('user_interests');
        $table
            ->addColumn('user_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('interest_slug', 'string', ['limit' => 20, 'null' => true, 'default' => null])
            ->create();

        $table
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('interest_slug', 'interests', 'slug', ['delete' => 'CASCADE'])
            ->update();
    }
}
