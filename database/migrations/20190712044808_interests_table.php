<?php

use Phinx\Migration\AbstractMigration;

class InterestsTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('interests', ['id' => false, ['primary_key' => ['slug']]]);
        $table
            ->addColumn('slug', 'string', ['limit' => 20])
            ->addColumn('name', 'string', ['limit' => 20])
            ->addColumn('description', 'string', ['limit' => 100, 'null' => true, 'default' => null])
            ->addIndex(['slug'], ['unique' => true])

            ->create();
    }
}
