<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Income.php';

$database = new Database();
$db = $database->getConnection();

$income_id = $_GET['income_id']; 


$income = new Income($db);
$income->delete($income_id);
 
http_response_code(200);
echo json_encode(['message'=>'Income transaction successfully deleted.']);
