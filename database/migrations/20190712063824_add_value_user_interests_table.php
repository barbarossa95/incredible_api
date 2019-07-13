<?php

use Phinx\Migration\AbstractMigration;

class AddValueUserInterestsTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('user_interests');
        $table
            ->addColumn('value', 'boolean', ['default' => true])
            ->update();
    }
}
