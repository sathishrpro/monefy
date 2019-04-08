<?php

class Transactions
{
	//db variables
	private $db_conn;
	
  
	public function __construct($db)
	{
		$this->db_conn = $db;
	}

	public function getTransactionsForUser($user_id, $filter=[])
	{
		$qry = "select * from (
					select i.income_id as id,
						   i.amount,
						   ic.income_category as category,
						   i.income_date as trans_date,
						   i.created_at ,
						   'I' as trans_type
						   from income i
						   join income_categories ic on ic.income_category_id=i.category_id 
						   where user_id=:user_id
						   union
						   select e.expense_id as id,
						   e.amount,
						   ec.expense_category as category,
						   e.expense_date as trans_date,
						   e.created_at,
						   'E' as trans_type
				 		   from expense e
				  		   join expense_categories ec on ec.expense_category_id=e.category_id
				  		   where user_id=:user_id) 
				  		   transactions ";

  		if(!empty($filter['trans_start_date']) && !empty($filter['trans_end_date']))
		{
			$qry .= " where trans_date between '"  . $filter['trans_start_date'] . "' and '" . $filter['trans_end_date'] . "'";
		}

		$qry .= " order by created_at desc";
 

		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$user_id = htmlspecialchars(strip_tags($user_id));
 		$statement->bindParam(':user_id', $user_id);
 		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function dashboard($user_id)
	{
		
	}
 
 

	 

}