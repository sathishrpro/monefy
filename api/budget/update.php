<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Budget.php';

$database = new Database();
$db = $database->getConnection();

$data['budget_id'] = $_POST['budget_id'];
$data['amount'] = $_POST['amount'];
$data['category_id'] = $_POST['category_id'];

$budget = new Budget($db);
$is_update = $budget->update($data);
if(!$is_update)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not update budget information. Try again.']);
	return;
}

http_response_code(200);
echo json_encode(['budget'=>$data, 'message'=>'Successfully budget information updated.']);
