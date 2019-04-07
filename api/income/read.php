<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Income.php';

$database = new Database();
$db = $database->getConnection();

$income_id = $_GET['income_id']; 


$income = new Income($db);
$income_transaction = $income->get($income_id);
if(!$income_transaction)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not find income transaction.']);
	return;
}
 

http_response_code(200);
echo json_encode(['income'=>$income_transaction]);
