<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Budget.php';

$database = new Database();
$db = $database->getConnection();

$data['amount'] = $_POST['amount'];
$data['user_id'] = $_POST['user_id'];
$data['category_id'] = $_POST['category_id'];

$budget = new Budget($db);
$budget_id = $budget->create($data);
if(!$budget_id)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not add budget information. Try again.']);
	return;
}

$data['budget_id'] = $budget_id;

http_response_code(200);
echo json_encode(['budget'=>$data, 'message'=>'Successfully budget information added.']);
