<?php

require $_SERVER['DOCUMENT_ROOT'] . '/scripts/helpers.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/Settings.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod !== 'POST') response(405);

$userId = authorize();

/**
 * Edit use settings
 */
function editUserSetting()
{
    return null;
}
