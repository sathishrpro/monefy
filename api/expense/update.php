<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Expense.php';

$database = new Database();
$db = $database->getConnection();

$data['expense_id'] = $_POST['expense_id'];
$data['amount'] = $_POST['amount'];
$data['category_id'] = $_POST['category_id'];
$data['expense_date'] = $_POST['expense_date'];
$data['recurring_cost_type_id'] = $_POST['recurring_cost_type_id'];

$expense = new Expense($db);
$is_update = $expense->update($data);
if(!$is_update)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not update expense transaction. Try again.']);
	return;
}
 

http_response_code(200);
echo json_encode(['expense'=>$data, 'message'=>'Successfully expense transaction updated.']);
