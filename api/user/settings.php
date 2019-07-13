<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/scripts/helpers.php';

use Models\User;
use Models\Interest;

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod !== 'POST') response(405);

$userId = authorize();

$interests = array_map(
    function ($item) {
        return $item->slug;
    },
    Interest::getAll()
);

$data = request();

$validationRules = [
    'age_limit_min' => [
        'required',
        'integer',
        ['in', [0, 18, 24, 40]]
    ],
    'age_limit_max' => [
        'required',
        'integer',
        ['in', [0, 18, 24, 40]]
    ],
    'age_search_min' => [
        'integer',
        ['in', [null, 18, 24, 40]]
    ],
    'age_search_max' => [
        'integer',
        ['in', [null, 18, 24, 40]]
    ],
    'location' => [
        'required',
        ['in', ['world', 'country', 'near']]
    ],
    'user_interests' => [
        'array',
        ['arrayHasKeys', $interests]
    ],
    'search_interests' => [
        'array',
        ['arrayHasKeys', $interests]
    ]
];

$errors = validate($data, $validationRules);

if ($errors) response(422, $errors);

$user = User::findById($userId);

if (!$user) response(403);

$user->fill($data);
$user->update();

$interests = $user->getInterests();
$settings = $user->getSettings();

foreach ($interests as $interest) {
    $newValue = $data['user_interests'][$interest->interest_slug];
    $interest->setValue($newValue);
}
foreach ($settings as $settingsItem) {
    $newValue = $data['search_interests'][$settingsItem->interest_slug];
    $settingsItem->setValue($newValue);
}

response(201);
