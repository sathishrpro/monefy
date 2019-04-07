<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Expense.php';

$database = new Database();
$db = $database->getConnection();

$data['amount'] = $_POST['amount'];
$data['user_id'] = $_POST['user_id'];
$data['category_id'] = $_POST['category_id'];
$data['expense_date'] = $_POST['expense_date'];
$data['recurring_cost_type_id'] = $_POST['recurring_cost_type_id'];


$expense = new Expense($db);
$expense_id = $expense->create($data);
if(!$expense_id)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not add expense transaction. Try again.']);
	return;
}

$data['expense_id'] = $expense_id;

http_response_code(200);
echo json_encode(['expense'=>$data, 'message'=>'Successfully expense transaction added.']);
