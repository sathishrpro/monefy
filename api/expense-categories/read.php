<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/ExpenseCategory.php';

$database = new Database();
$db = $database->getConnection();

$expense_category = new ExpenseCategory($db);
$db_statement = $expense_category->getAll();
$total_rows = $db_statement->rowCount();

if($total_rows==0)
{
	http_response_code(404);
	echo json_encode(['message'=>'No expense category found.']);
	return;
}

$expense_categories = [];

while($row = $db_statement->fetch(PDO::FETCH_ASSOC))
{
	$expense_category = [
						  'id' => $row['expense_category_id'], 
						  'category' => $row['expense_category']
						];
	array_push($expense_categories, $expense_category);
}

http_response_code(200);
echo json_encode($expense_categories);
