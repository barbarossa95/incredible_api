<?php

$rootDir = $_SERVER['DOCUMENT_ROOT'];

include("$rootDir/scripts/connection.php");
include("$rootDir/scripts/helpers.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
  case 'GET': // todo
  case 'POST':
    $user = registerUser();
    break;
  default:
    response(501);
    break;
};

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
