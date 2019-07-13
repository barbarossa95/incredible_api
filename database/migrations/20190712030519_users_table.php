<?php

use Phinx\Migration\AbstractMigration;

class UsersTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('name', 'string', ['limit' => 20])
            ->addColumn('email', 'string', ['limit' => 40])
            ->addColumn('password', 'string', ['limit' => 60])
            ->addColumn('last_login_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('birthdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}
