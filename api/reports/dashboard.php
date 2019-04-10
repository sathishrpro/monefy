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
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

if(isset($_GET['start_date']))
{
	$start_date = $_GET['start_date'];
}

if(isset($_GET['end_date']))
{
	$end_date = $_GET['end_date'];
}
  

$transactions = new Transactions($db);
$dashboard_data = $transactions->getDashboardData($user_id, $start_date,$end_date);
 
http_response_code(200);
echo json_encode(['dashboard_data'=>$dashboard_data]);
