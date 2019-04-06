<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/IncomeCategory.php';

$database = new Database();
$db = $database->getConnection();

$income_category = new IncomeCategory($db);
$dbStatement = $income_category->getAll();
$total_rows = $dbStatement->rowCount();

if($total_rows==0)
{
	http_response_code(404);
	echo json_encode(['message'=>'No income category found.']);
	return;
}

$income_categories = [];

while($row = $dbStatement->fetch(PDO::FETCH_ASSOC))
{
	$income_category = [
						  'id' => $row['income_category_id'], 
						  'category' => $row['income_category']
						];
	array_push($income_categories, $income_category);
}

http_response_code(200);
echo json_encode($income_categories);
