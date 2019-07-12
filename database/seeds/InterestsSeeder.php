<?php

use Phinx\Seed\AbstractSeed;

class InterestsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 3; $i++) {
            $row = [];
            $row['slug'] = "interest$i";
            $row['name'] = "Интерес $i";
            $row['description'] = "Эпичное описание интереса $i";
            $data[] = $row;
        }

        $interests = $this->table('interests');

        $this->execute('SET foreign_key_checks=0');
        $interests->truncate();
        $this->execute('SET foreign_key_checks=1');
        $interests
            ->insert($data)
            ->save();
    }
}
