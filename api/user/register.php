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

  // todo create jwt

  // response with user and jwt
  response(200, $data);
}
*/
