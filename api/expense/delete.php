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
$expense->delete($expense_id);
 
http_response_code(200);
echo json_encode(['message'=>'Expense transaction successfully deleted.']);
