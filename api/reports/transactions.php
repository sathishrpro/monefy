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
$filter = [];

if(isset($_GET['trans_start_date']) && isset($_GET['trans_end_date']))
{
	$filter['trans_start_date'] = $_GET['trans_start_date'];
	$filter['trans_end_date'] = $_GET['trans_end_date'];
}


$transactions = new Transactions($db);
$user_transactions = $transactions->getTransactionsForUser($user_id, $filter);
if(empty($user_transactions))
{
	http_response_code(404);
	echo json_encode(['message'=>'No Income/Expense transaction found.']);
	return;
}

http_response_code(200);
echo json_encode(['user_transactions'=>$user_transactions]);
