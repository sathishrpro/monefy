<?php

class Income
{
	//db variables
	private $db_conn;
	private $table = 'income';

	//user properties
	private $income_id;
	private $amount;
	private $category_id;
	private $income_date;
	private $user_id;
	private $created_at;
	private $updated_at;


	public function __construct($db)
	{
		$this->db_conn = $db;
	}

	public function create($income)
	{
		$qry = 'insert into ' . $this->table . '(user_id, amount, category_id, income_date) values (:user_id, :amount, :category_id, :income_date)';
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($income['user_id']));
		$this->amount = htmlspecialchars(strip_tags($income['amount']));
		$this->category_id = htmlspecialchars(strip_tags($income['category_id']));
		$this->income_date = htmlspecialchars(strip_tags($income['income_date']));
		 
 		$statement->bindParam(':user_id', $this->user_id);
		$statement->bindParam(':amount', $this->amount);
		$statement->bindParam(':category_id', $this->category_id);
		$statement->bindParam(':income_date', $this->income_date);

 		if($statement->execute())
		{
			return $this->db_conn->lastInsertId();
		}
		return false;
	}

	public function get($income_id)
	{
		$qry = "select i.income_id, i.amount, i.category_id, i.income_date, ic.income_category from " 
				. $this->table . 
				" i join  income_categories ic on ic.income_category_id=i.category_id where income_id = :income_id";
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->income_id = htmlspecialchars(strip_tags($income_id));

		$statement->bindParam(':income_id', $this->income_id);

		$statement->execute();

		if($statement->rowCount()>0)
		{
			return $statement->fetch(PDO::FETCH_ASSOC);
		}
		return null;
	}

	public function update($income)
	{
		$qry = 'update ' . $this->table . ' set amount=:amount, category_id=:category_id, 
								  income_date= :income_date where income_id=:income_id';
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
 		$this->amount = htmlspecialchars(strip_tags($income['amount']));
		$this->category_id = htmlspecialchars(strip_tags($income['category_id']));
		$this->income_date = htmlspecialchars(strip_tags($income['income_date']));
		$this->income_id = htmlspecialchars(strip_tags($income['income_id']));
		 
 		$statement->bindParam(':amount', $this->amount);
		$statement->bindParam(':category_id', $this->category_id);
		$statement->bindParam(':income_date', $this->income_date);
		$statement->bindParam(':income_id', $this->income_id);

 		if($statement->execute())
		{
			return true;
		}
		return false;
	}


	public function delete($income_id)
	{
		$qry = "delete from " . $this->table . " where income_id = :income_id";
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->income_id = htmlspecialchars(strip_tags($income_id));

		$statement->bindParam(':income_id', $this->income_id);

		$statement->execute();
	}

	public function getTotalIncome($user_id, $start_date, $end_date)
	{
		$qry = "select sum(amount) as total_income from income where user_id=:user_id
					   and  income_date between :start_date and :end_date";
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($user_id));
		$start_date = htmlspecialchars(strip_tags($start_date));
		$end_date = htmlspecialchars(strip_tags($end_date));

		$statement->bindParam(':user_id', $this->user_id);
		$statement->bindParam(':start_date', $start_date);
		$statement->bindParam(':end_date', $end_date);

		$statement->execute();

		return $statement->fetchColumn();

	}

	 

}