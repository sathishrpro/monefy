<?php

class Database {
	//DB params
	private $host = 'localhost';
	private $userName = 'root';
	private $password = '';
	private $dbName = 'monefy';
	private $dbConn;

	public function getConnection()
	{
		$this->dbConn = null;

		try
		{
			$this->dbConn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbName, $this->userName, $this->password);
			$this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			echo 'Connection Error:' . $e->getMessage();
		}
		return $this->dbConn;
	}

}