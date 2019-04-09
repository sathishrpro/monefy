<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Transactions.php';

$database = new Database();
$db = $database->getConnection();


$user_id = $_GET['user_id'];
$month = $_GET['month'];
$year = $_GET['year'];
 

$transactions = new Transactions($db);
$dashboard_data = $transactions->getDashboardData($user_id, $month,$year);
 
http_response_code(200);
echo json_encode(['dashboard_data'=>$dashboard_data]);
