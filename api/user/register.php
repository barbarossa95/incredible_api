<?php
$rootDir = $_SERVER['DOCUMENT_ROOT'];
include($rootDir . "/database/connection.php");
include($rootDir . "/helpers.php");

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
  case 'GET': // todo
  case 'POST':
    echo "gotch ya";

    break;
  default:
    echo "gotch ya holmi";
    break;
};
// $pdo = DB::getInstance();

function registerUser()
{
  $allowed = [
    'email',
    'password',
    'password_confirmation',
    'name',
    'birthdate',
    'interests',
    'avatar'
  ];
  $data = filterArrayByKeys($_POST, $allowed);

  // todo: validate data
  // sql on create user
  // create jwt
  // response with user and jwt
}
