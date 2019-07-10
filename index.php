<?php

include("./database/connection.php");

$pdo = DB::getInstance();



$data = ['test' => true];
header('Content-Type: application/json');
echo json_encode($data);


// echo "<pre>";

// $sqlQuery = "CREATE TABLE contacts (
//   id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   firstName VARCHAR(35) NOT NULL,
//   lastName VARCHAR(35) NOT NULL,
//   email VARCHAR(55)
//   )";

// if ($pdo->query($sqlQuery) !== false) {
//   echo "Table created successfully!";
// } else {
//   echo "SQL error: " . $pdo->errorCode();
// }
