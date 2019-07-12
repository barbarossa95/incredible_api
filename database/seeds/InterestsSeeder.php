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
            $data[] = [
                'slug' => "interest$i",
                'name' => "Интерес $i",
                'description' => "Эпичное описание интереса $i"
            ];
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
