<?php

use Phinx\Migration\AbstractMigration;

class AddLatLongUserTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('lat', 'decimal', ['precision' => 10, 'scale' => 7, 'null' => true, 'default' => null])
            ->addColumn('long', 'decimal', ['precision' => 10, 'scale' => 7, 'null' => true, 'default' => null])
            ->update();
    }
}
