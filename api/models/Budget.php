<?php

class Budget
{
	//db variables
	private $db_conn;
	private $table = 'budget';

	//user properties
	private $budget_id;
	private $amount;
	private $category_id;
	private $user_id;
	private $created_at;
	private $updated_at;


	public function __construct($db)
	{
		$this->db_conn = $db;
	}

	public function create($budget)
	{
		$qry = 'insert into ' . $this->table . '(user_id, amount, category_id) values (:user_id, :amount, :category_id)';
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($budget['user_id']));
		$this->amount = htmlspecialchars(strip_tags($budget['amount']));
		$this->category_id = htmlspecialchars(strip_tags($budget['category_id']));
 		 
 		$statement->bindParam(':user_id', $this->user_id);
		$statement->bindParam(':amount', $this->amount);
		$statement->bindParam(':category_id', $this->category_id);
 
 		if($statement->execute())
		{
			return $this->db_conn->lastInsertId();
		}
		return false;
	}

	public function getAllForUser($user_id)
	{
		$qry = "select b.budget_id, b.amount, b.category_id, ec.expense_category from " 
				. $this->table . 
				" b join  expense_categories ec on ec.expense_category_id=b.category_id where b.user_id = :user_id";
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($user_id));

		$statement->bindParam(':user_id', $this->user_id);

		$statement->execute();

		if($statement->rowCount()>0)
		{
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		return [];
	}

	public function update($budget)
	{
		$qry = 'update ' . $this->table . ' set amount=:amount, category_id=:category_id where budget_id=:budget_id';
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
 		$this->amount = htmlspecialchars(strip_tags($budget['amount']));
		$this->category_id = htmlspecialchars(strip_tags($budget['category_id']));
		$this->budget_id = htmlspecialchars(strip_tags($budget['budget_id']));
		 
 		$statement->bindParam(':amount', $this->amount);
		$statement->bindParam(':category_id', $this->category_id);
		$statement->bindParam(':budget_id', $this->budget_id);

 		if($statement->execute())
		{
			return true;
		}
		return false;
	}


	public function delete($budget_id)
	{
		$qry = "delete from " . $this->table . " where budget_id = :budget_id";
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$this->budget_id = htmlspecialchars(strip_tags($budget_id));

		$statement->bindParam(':budget_id', $this->budget_id);

		$statement->execute();
	}

	public function getTotalBudget($user_id)
	{
		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($user_id));
		
		$total_budget_qry = 'select sum(amount) from budget where user_id=:user_id';
		$statement = $this->db_conn->prepare($total_budget_qry);
		$statement->bindParam(':user_id', $user_id);
		$statement->execute();
		$total_budget = $statement->fetchColumn();
		if($total_budget != null)
		{
			return $total_budget;
		}
		return 0;
	}

	public function getUserBudgetCategories($user_id)
	{
		//sanitize input
		$this->user_id = htmlspecialchars(strip_tags($user_id));

		$user_not_added_budget_categories_qry = "select expense_category_id, expense_category 
												 from expense_categories
												 where expense_category_id not in 
												 (select category_id from budget where user_id=:user_id)";
		$statement = $this->db_conn->prepare($user_not_added_budget_categories_qry);
		$statement->bindParam(':user_id', $user_id);
		$statement->execute();
		$expense_categories = $statement->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($expense_categories))
		{
			return $expense_categories;
		}
		return [];	
	}

	 

}