<?php

class IncomeCategory
{
	//db variables
	private $dbConn;
	private $table = 'income_categories';
 
	//properties
	private $income_category_id;
	private $income_category;	 

	public function __construct($db)
	{
		$this->dbConn = $db;
	}

	public function getAll()
	{
		$qry = 'select income_category_id,
					  income_category
					  from ' . $this->table ;

		$dbStatement = $this->dbConn->prepare($qry);
		$dbStatement->execute();
		return $dbStatement;
	}

}