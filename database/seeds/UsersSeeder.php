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
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $ip = $faker->ipv4;
            $geoData = $this->parseGeoIpInfo($ip);
            $data = [
                'name'          => substr($faker->name, 0, 19),
                'email'         => substr($faker->email, 0, 19),
                'password'      => password_hash('321', PASSWORD_BCRYPT, array('cost' => 12)),
                'last_login_at' => $faker->date('Y-m-d H:i:s', 'now'),
                'birthdate'     => $faker->date('Y-m-d H:i:s', 'now'),
                'lat'           => $geoData['geoplugin_latitude'],
                'long'          => $geoData['geoplugin_longitude'],
                'country_code'  => $geoData['geoplugin_countryCode'],
            ];
            $this->table('users')->insert($data)->save();
        }
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
}
