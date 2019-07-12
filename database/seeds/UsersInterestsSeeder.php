<?php


use Phinx\Seed\AbstractSeed;

class UsersInterestsSeeder extends AbstractSeed
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
        $users = $this->fetchAll('SELECT id FROM users');
        $interests = $this->fetchAll('SELECT slug FROM interests');
        $userInterests = $this->table('user_interests');

        $this->execute('SET foreign_key_checks=0');
        $userInterests->truncate();
        $this->execute('SET foreign_key_checks=1');

        foreach ($users as $user) {
            foreach ($interests as $interest) {
                $userInterests->insert([
                    [
                        'user_id' => $user['id'],
                        'interest_slug' => $interest['slug'],
                        'value' => mt_rand(0, 1)
                    ]
                ])->save();
            }
        }
    }
}
