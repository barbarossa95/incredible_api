<?php

use \Firebase\JWT\JWT;

/**
 * Request helper function to get JSON input data
 *
 * @return array
 */
function request()
{
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    return $input;
}

/**
 * Response helper function
 *
 * @param integer $code http status code
 * @param array $data = null data to send client. Default null
 * @return void
 */
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

/**
 * Exclude all elements wich is not represented in second arg
 *
 * @param array $array origin array
 * @param array $allowed array of allowed keys
 * @return array
 */
function filterArrayByKeys($array, $allowed)
{
    return array_intersect_key($array, array_flip($allowed));
}

/**
 * Parse ip geo data
 *
 * @param string $ip user ip
 * @return array
 */
function parseGeoIpInfo($ip)
{
    $ip_data = file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip");

    return json_decode($ip_data, true);
}

/**
 * Get header Authorization
 *
 * @return string|null
 */
function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * Get access token from header
 *
 * @return string|null
 */
function getBearerToken()
{
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/**
 * Authorize user or restrict access if not authorized
 *
 * @return void
 */
function authorize()
{
    $token = getBearerToken();

    if (!$token) response(403);

    $payload = decodeToken($token);

    if (!$payload) response(403);

    $_REQUEST['user_id'] = $payload['user_id'];
}

/**
 * Decode token payload
 *
 * @param string $token
 * @return array|null
 */
function decodeToken($token)
{
    try {
        // todo: decode token here
        $payload = $token;
        $payload = null;
        return $payload;
    } catch (Exception $ex) {
        return null;
    }
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
