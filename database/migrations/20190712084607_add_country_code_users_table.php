<?php

use Phinx\Migration\AbstractMigration;

class AddCountryCodeUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $this->table('users')
            ->addColumn('country_code', 'string', ['limit' => 10, 'null' => true, 'default' => null])
            ->update();
    }
}
