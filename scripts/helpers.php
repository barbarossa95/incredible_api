<?php

function request()
{
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true); //convert JSON into array
    return $input;
}

function response($code, $data = null)
{
    http_response_code($code);
    // Тип возвращаемого ответа api зависит от кода статуса
    switch ($code) {
        case 201: // ok
            break;
        case 200: // created
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($data);
        case 400: // bad req
        case 403: // forbidden
        case 422: // validation error
        case 500: // internal error
        case 501: // not implemented
        default:
            header('Content-Type: text/plain; charset=UTF-8');
            echo $data;
            break;
    }
}

function filterArrayByKeys($array, $allowed)
{
    return array_intersect_key($array, array_flip($allowed));
}

function parseGeoIpInfo($ip)
{
    $ip_data =  file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip");

    return json_decode($ip_data, true);
}
