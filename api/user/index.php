<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/scripts/helpers.php';

use Models\User;

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod !== 'GET') response(405);

try {
    $response = getFeed();

    if ($response) response(200, $response);

    if (is_array($response)) response(404);

    response(403);
} catch (Exception $ex) {
    response(500, $ex);
}

/**
 * Login user action
 */
function getFeed()
{
    $offset = $_GET['page'] ?? 0;
    $offset *= 10;

    $validationRules = [
        'page' => [
            'integer',
        ]
    ];

    $errors = validate($_GET, $validationRules);

    if ($errors) response(422, $errors);

    $userId = authorize();

    $user = User::findById($userId);

    if (!$user) response(403);

    return $user->getUsersFeed($offset);
}
