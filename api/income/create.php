<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Income.php';

$database = new Database();
$db = $database->getConnection();

$data['amount'] = $_POST['amount'];
$data['user_id'] = $_POST['user_id'];
$data['category_id'] = $_POST['category_id'];
$data['income_date'] = $_POST['income_date'];


$income = new Income($db);
$income_id = $income->create($data);
if(!$income_id)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not add income transaction. Try again.']);
	return;
}

$data['income_id'] = $income_id;

http_response_code(200);
echo json_encode(['income'=>$data, 'message'=>'Successfully income transaction added.']);
