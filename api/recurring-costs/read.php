<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/RecurringCost.php';

$database = new Database();
$db = $database->getConnection();

$recurring_cost = new RecurringCost($db);
$db_statement = $recurring_cost->getAll();
$total_rows = $db_statement->rowCount();

if($total_rows==0)
{
	http_response_code(404);
	echo json_encode(['message'=>'No recurring cost type found.']);
	return;
}

$recurring_costs = [];

while($row = $db_statement->fetch(PDO::FETCH_ASSOC))
{
	$recurring_cost = [
						  'id' => $row['recurring_cost_type_id'], 
						  'recurring_cost_type' => $row['recurring_cost_type']
						];
	array_push($recurring_costs, $recurring_cost);
}

http_response_code(200);
echo json_encode($recurring_costs);
