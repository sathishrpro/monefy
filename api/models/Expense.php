<?php

class Expense
{
	//db variables
	private $db_conn;
	private $table = 'expense';

	//user properties
	private $expense_id;
	private $amount;
	private $category_id;
	private $expense_date;
	private $user_id;
	private $recurring_cost_type_id;
	private $created_at;
	private $updated_at;


	public function __construct($db)
	{
		$this->db_conn = $db;
	}

	
	public function create($expense)
	{
		$qry = 'insert into ' . $this->table . '(user_id, amount, category_id, expense_date, recurring_cost_type_id) values (:user_id, :amount, :category_id, :expense_date, :recurring_cost_type_id)';
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($expense['user_id']));
		$this->amount = htmlspecialchars(strip_tags($expense['amount']));
		$this->category_id = htmlspecialchars(strip_tags($expense['category_id']));
		$this->expense_date = htmlspecialchars(strip_tags($expense['expense_date']));
		$this->recurring_cost_type_id = htmlspecialchars(strip_tags($expense['recurring_cost_type_id']));
		 
 		$statement->bindParam(':user_id', $this->user_id);
		$statement->bindParam(':amount', $this->amount);
		$statement->bindParam(':category_id', $this->category_id);
		$statement->bindParam(':expense_date', $this->expense_date);
		$statement->bindParam(':recurring_cost_type_id', $this->recurring_cost_type_id);

 		if($statement->execute())
		{
			return $this->db_conn->lastInsertId();
		}
		return false;
	}

	public function get($expense_id)
	{
		$qry = "select e.expense_id, e.amount, e.category_id, e.expense_date, ec.expense_category, rct.recurring_cost_type, e.recurring_cost_type_id from " 
				. $this->table . 
				" e join expense_categories ec on ec.expense_category_id=e.category_id 
				 join recurring_costs_type rct on rct.recurring_cost_type_id= e.recurring_cost_type_id
				 where expense_id = :expense_id";
		$statement = $this->db_conn->prepare($qry);
 
 		//sanitize input
		$this->expense_id = htmlspecialchars(strip_tags($expense_id));

		$statement->bindParam(':expense_id', $this->expense_id);

		$statement->execute();

		if($statement->rowCount()>0)
		{
			return $statement->fetch(PDO::FETCH_ASSOC);
		}
		return null;
	}

	public function update($expense)
	{
		$qry = 'update ' . $this->table . ' set amount=:amount, category_id=:category_id, 
								  expense_date= :expense_date , recurring_cost_type_id=:recurring_cost_type_id where expense_id=:expense_id';
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
 		$this->amount = htmlspecialchars(strip_tags($expense['amount']));
		$this->category_id = htmlspecialchars(strip_tags($expense['category_id']));
		$this->expense_date = htmlspecialchars(strip_tags($expense['expense_date']));
		$this->expense_id = htmlspecialchars(strip_tags($expense['expense_id']));
		$this->recurring_cost_type_id = htmlspecialchars(strip_tags($expense['recurring_cost_type_id']));
		 
 		$statement->bindParam(':amount', $this->amount);
		$statement->bindParam(':category_id', $this->category_id);
		$statement->bindParam(':expense_date', $this->expense_date);
		$statement->bindParam(':expense_id', $this->expense_id);
		$statement->bindParam(':recurring_cost_type_id', $this->recurring_cost_type_id);

 		if($statement->execute())
		{
			return true;
		}
		return false;
	}
 
	public function delete($expense_id)
	{
		$qry = "delete from " . $this->table . " where expense_id = :expense_id";
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->expense_id = htmlspecialchars(strip_tags($expense_id));

		$statement->bindParam(':expense_id', $this->expense_id);
		$statement->execute();
	}

	 

}