<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';
include_once '../models/Budget.php';

$database = new Database();
$db = $database->getConnection();

$budget_id = $_GET['budget_id']; 

$budget = new Budget($db);
$budget->delete($budget_id);
 
http_response_code(200);
echo json_encode(['message'=>'Budget information successfully deleted.']);
