<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Expense.php';

$database = new Database();
$db = $database->getConnection();

$expense_id = $_GET['expense_id']; 


$expense = new Expense($db);
$expense_transaction = $expense->get($expense_id);
if(!$expense_transaction)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not find expense transaction.']);
	return;
}
 
http_response_code(200);
echo json_encode(['expense'=>$expense_transaction]);
