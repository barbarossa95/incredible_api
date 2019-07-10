<?php

function response($data)
{
  header('Content-Type: application/json');
  echo json_encode($data);
}

function filterArrayByKeys($array, $allowed)
{
  return array_intersect_key($array, array_flip($allowed));
}
