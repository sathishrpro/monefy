<?php

class RecurringCost
{
	//db variables
	private $dbConn;
	private $table = 'recurring_costs_type';
 
	//properties
	private $recurring_cost_type_id;
	private $recurring_cost_type;	 

	public function __construct($db)
	{
		$this->dbConn = $db;
	}

	public function getAll()
	{
		$qry = 'select recurring_cost_type_id,
					  recurring_cost_type
					  from ' . $this->table ;

		$dbStatement = $this->dbConn->prepare($qry);
		$dbStatement->execute();
		return $dbStatement;
	}

}