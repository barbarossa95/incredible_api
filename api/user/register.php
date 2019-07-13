<?php

require $_SERVER['DOCUMENT_ROOT'] . '/scripts/helpers.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod !== 'POST') response(405);

$user = registerUser();

if ($user) response(201);

response(501, 'some server error');

/*
function registerUser()
{
    $pdo = DB::getInstance();

    $data = request();
    $allowed = [
        'email',
        'password',
        'password_confirmation',
        'name',
        'birthdate',
        'interests',
        'avatar'
    ];

    $data = filterArrayByKeys(request(), $allowed);

    $data = array_map(function ($item) use ($pdo) {
        return $pdo->quote($item);
    }, $data);

    // todo: validate data

    // sql on create user
    $userQuery = $pdo->prepare("
    INSERT INTO users (email, password, name, birthdate, avatar)
    VALUES (:email, :password, :name, :birthdate, :avatar)");
    $userQuery->bindParam(':email', $data['email']);
    $userQuery->bindParam(':password', $data);
    $userQuery->bindParam(':name', $data['name']);
    $userQuery->bindParam(':birthdate', $data['birthdate']);
    $userQuery->bindParam(':avatar', $data['avatar']);
    $userQuery->execute();

    // todo create jwt

    // response with user and jwt
    response(200, $data);
}
*/
