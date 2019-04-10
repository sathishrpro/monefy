<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Budget.php';

$database = new Database();
$db = $database->getConnection();

$user_id = $_GET['user_id']; 


$budget = new Budget($db);
$budget_details = $budget->getAllForUser($user_id);
if(empty($budget_details))
{
	http_response_code(404);
	echo json_encode(['message'=>'No budget information found.']);
	return;
}

$budget_categories_amount = array_column($budget_details, 'amount');

$total_budget = array_sum($budget_categories_amount);
 

http_response_code(200);
echo json_encode(['budget'=>$budget_details, 'total_budget'=>$total_budget]);
