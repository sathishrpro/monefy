<?php

class ExpenseCategory
{
	//db variables
	private $dbConn;
	private $table = 'expense_categories';
 
	//properties
	private $expense_category_id;
	private $expense_category;	 

	public function __construct($db)
	{
		$this->dbConn = $db;
	}

	public function getAll()
	{
		$qry = 'select expense_category_id,
					  expense_category
					  from ' . $this->table ;

		$dbStatement = $this->dbConn->prepare($qry);
		$dbStatement->execute();
		return $dbStatement;
	}

}