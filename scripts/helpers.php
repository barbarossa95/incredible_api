<?php

use \Firebase\JWT\JWT;

function request()
{
  $inputJSON = file_get_contents('php://input');
  $input = json_decode($inputJSON, true); //convert JSON into array
  return $input;
}

function response($code, $data = null)
{
  http_response_code($code);
  if ($code == 200) header('Content-Type: application/json; charset=UTF-8');

  if ($code !== 200) header('Content-Type: text/plain; charset=UTF-8');

  // Тип возвращаемого ответа api зависит от кода статуса
  switch ($code) {
    case 201: // ok
      exit();
    case 200: // created
      echo json_encode($data);
      exit();
    case 400:
      echo 'Bad request';
      break;
    case 403:
      echo 'Forbidden';
      break;
    case 405:
      echo 'Method not allowed';
      break;
    case 422:
      echo 'Validation error';
      break;
    case 500:
      echo 'Internal server error';
      break;
    case 501:
    default:
      echo 'Not implemented';
      break;
  }

  echo PHP_EOL . $data;
  exit();
}

function filterArrayByKeys($array, $allowed)
{
  return array_intersect_key($array, array_flip($allowed));
}

function generateToken($user)
{
  $key = "example_key"; // строка для примера, в реальом проекте следует выносить ключ шифрования в переменные среды
  $token = [
    "iss" => "http://incredible.test",
    "aud" => "http://incredible.test",
    "iat" => strtotime("now"),
    "exp" => strtotime("+ 1 day"),
    "user" => $user->id
  ];
}
