<?php

class User
{
	//db variables
	private $db_conn;
	private $table = 'users';

	//user properties
	private $user_id;
	private $user_name;
	private $password;
	private $created_at;
	private $updated_at;


	public function __construct($db)
	{
		$this->db_conn = $db;
	}

	public function login($user_name, $password)
	{
		$qry = 'select user_id, user_name, password from users where user_name=:user_name and password=:password';
		$statement = $this->db_conn->prepare($qry);
		$this->user_name = htmlspecialchars(strip_tags($user_name));
		$this->password = sha1(htmlspecialchars(strip_tags($password))); //hash password

		$statement->bindParam(":user_name", $this->user_name);
		$statement->bindParam(":password", $this->password);

		$statement->execute();

		$total_row = $statement->rowCount();

		if($total_row==1)
		{
			$user_row = $statement->fetch(PDO::FETCH_ASSOC);
			return $user_row['user_id'];
		}

		return false;
	}

	public function signup($user_name, $password)
	{
		$this->user_name = htmlspecialchars(strip_tags($user_name));
		$this->password = sha1(htmlspecialchars(strip_tags($password))); //hash password
		
		if($this->userNameExists($this->user_name))
		{
			throw new Exception('User name already exists');
		}
	
		$qry = 'insert into users (user_name, password) values (:user_name,:password)';
		$statement = $this->db_conn->prepare($qry);
	
		$statement->bindParam(":user_name", $this->user_name);
		$statement->bindParam(":password", $this->password);

		if($statement->execute())
		{
			return $this->db_conn->lastInsertId();
		}
		return false;		

	}

	public function userNameExists($user_name)
	{
		$qry = "select user_name from users where user_name=:user_name";
		$statement = $this->db_conn->prepare($qry);
		$user_name = htmlspecialchars(strip_tags($user_name));
		$statement->bindParam(":user_name", $user_name);
		$statement->execute();
		if($statement->rowCount() >0)
		{
			return true;
		}
		return false;
	}

}