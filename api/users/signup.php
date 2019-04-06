<?php

//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 

//database connection
include_once '../config/Database.php';

include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user_name = $_POST['user_name'];
$password = $_POST['password'];

 $user = new User($db);
 try
 {
 	$user_id = $user->signup($user_name, $password);	

	$user_detail['user_id'] = $user_id;
	$user_detail['user_name'] = $user_name;

	http_response_code(200);
	echo json_encode(['user'=>$user_detail, 'message'=>'Successfully registered.']);
	return;
 }
 catch(Exception $e)
 {
 	http_response_code(400);
 	echo json_encode(['message'=>$e->getMessage()]);
 	return;
 }
 

