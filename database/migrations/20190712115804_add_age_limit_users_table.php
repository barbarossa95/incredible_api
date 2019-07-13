<?php

use Phinx\Migration\AbstractMigration;

class AddAgeLimitUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('age_limit_min', 'integer', ['null' => true, 'default' => null])
            ->addColumn('age_limit_max', 'integer', ['null' => true, 'default' => null])
            ->update();
    }
}
