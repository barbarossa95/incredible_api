<?php

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
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
        $users = $this->table('users');

        $this->execute('SET foreign_key_checks=0');
        $users->truncate();
        $this->execute('SET foreign_key_checks=1');

        $faker = Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $geoData = $this->getGeoData($faker);
            $ageLimitData = $this->getAgeLimitData();
            $data = [
                'name'          => substr($faker->name, 0, 19),
                'email'         => substr($faker->email, 0, 19),
                'password'      => password_hash('321', PASSWORD_BCRYPT, array('cost' => 12)),
                'last_login_at' => $faker->date('Y-m-d H:i:s', 'now'),
                'birthdate'     => $faker->date('Y-m-d H:i:s', 'now'),
                'lat'           => $geoData['geoplugin_latitude'],
                'long'          => $geoData['geoplugin_longitude'],
                'country_code'  => $geoData['geoplugin_countryCode'],
                'age_limit_min' => $ageLimitData['min'],
                'age_limit_max' => $ageLimitData['max'],
            ];
            $users->insert($data)->save();
        }
    }


    /**
     * Get Ip parsed data or get random Omks Location
     *
     * @param Faker\Factory $faker
     * @return array
     */
    public function getGeoData($faker)
    {
        if ($faker->boolean) {
            return $this->parseGeoIpInfo($faker->ipv4);
        }

        // Random Omsk Location
        return [
            'geoplugin_latitude' => (54 + $this->randomFloat()),
            'geoplugin_longitude' => (73 + $this->randomFloat()),
            'geoplugin_countryCode' => 'RU'
        ];
    }

    /**
     * Get age limit preferences
     *
     * @return array
     */
    public function getAgeLimitData()
    {
        $data = [
            ['min' => null, 'max' => null],
            ['min' => 18, 'max' => 24],
            ['min' => 24, 'max' => 40],
            ['min' => 40, 'max' => null],
        ];
        return $data[mt_rand(0, 3)];
    }

    /**
     * Parse ip geo data
     *
     * @param string $ip user ip
     * @return array
     */
    public function parseGeoIpInfo($ip)
    {
        $ip_data = file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip");

        return json_decode($ip_data, true);
    }

    /**
     * Get Random Float from 0 to 1
     *
     * @return float
     */
    protected function randomFloat()
    {
        return (float) rand() / (float) getrandmax();
    }
}
