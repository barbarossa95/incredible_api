<?php

require $_SERVER['DOCUMENT_ROOT'] . '/scripts/helpers.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/User.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod !== 'POST') response(405);

try {
    $response = registerUser();

    if ($response) response(200, $response);

    response(500, 'some server error');
} catch (Exception $ex) {
    response(500, $ex);
}

/**
 * Register user action
 */
function registerUser()
{
    $data = request();

    $validationRules = [
        'name' => [
            'required',
            ['lengthMax', 20]
        ],
        'email' => [
            'required',
            'email',
            ['lengthMax', 40]
        ],
        'password' => [
            'required',
            ['lengthMin', 4]
        ],
        'birthdate' => [
            'required',
            'date'
        ],

    ];

    $errors = validate($data, $validationRules);

    if ($errors) {
        response(422, $errors);
    }

    if (User::isEmailTaken($data['email'])) {
        response(422, [
            'email' => ['This email is already taken']
        ]);
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, array('cost' => 12));
    $user = new User;
    $user->email = $data['email'];
    $user->password = $hashedPassword;
    $user->name = $data['name'];
    $user->birthdate = $data['birthdate'];

    /* for production mode need to parse data
    $geoData = parseGeoIpInfo($_SERVER['REMOTE_ADDR']);
    $user->lat = $geoData['geoplugin_latitude'];
    $user->long = $geoData['geoplugin_longitude'];
    $user->country_code = $geoData['geoplugin_countryCode'];
    */

    if (!$user->create()) return null;

    return ['token' => generateToken($user)];
}
