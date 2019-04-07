<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/Income.php';

$database = new Database();
$db = $database->getConnection();

$data['income_id'] = $_POST['income_id'];
$data['amount'] = $_POST['amount'];
$data['category_id'] = $_POST['category_id'];
$data['income_date'] = $_POST['income_date'];


$income = new Income($db);
$is_update = $income->update($data);
if(!$is_update)
{
	http_response_code(404);
	echo json_encode(['message'=>'Could not updated income transaction. Try again.']);
	return;
}
 

http_response_code(200);
echo json_encode(['income'=>$data, 'message'=>'Successfully income transaction updated.']);
