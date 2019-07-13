<?php

use Phinx\Migration\AbstractMigration;

class AddSettingsUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('age_search_min', 'integer', ['null' => true, 'default' => null])
            ->addColumn('age_search_max', 'integer', ['null' => true, 'default' => null])
            ->addColumn('location', 'string', ['limit' => 20, 'default' => 'world'])
            ->update();
    }
}
