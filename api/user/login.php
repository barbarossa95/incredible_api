<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/scripts/helpers.php';

use Models\User;

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod !== 'POST') response(405);

try {
    $response = tryLoginUser();

    if ($response) response(200, $response);

    response(403);
} catch (Exception $ex) {
    response(500, $ex);
}

/**
 * Login user action
 */
function tryLoginUser()
{
    $data = request();

    $validationRules = [
        'email' => [
            'required',
        ],
        'password' => [
            'required',
        ],
    ];

    $errors = validate($data, $validationRules);

    if ($errors) {
        response(422, $errors);
    }

    $user = User::findByEmail($data['email']);

    if (!$user) response(403);

    if (!password_verify($data['password'], $user->password)) response(403);

    $user->last_login_at = date("Y-m-d H:i:s");
    $user->update();

    return [
        'token' => generateToken($user)
    ];
}
