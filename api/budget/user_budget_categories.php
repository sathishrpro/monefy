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
$budget_categories = $budget->getUserBudgetCategories($user_id);
if(empty($budget_categories))
{
	http_response_code(404);
	echo json_encode(['message'=>'No budget categories found.']);
	return;
}
 

http_response_code(200);
echo json_encode($budget_categories);
