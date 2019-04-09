<?php
include('Budget.php');
include('Income.php');
include('Expense.php');
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

	public function getDashboardData($user_id, $month,$year)
	{
		$dashboard_data['total_income'] =0;
		$dashboard_data['total_expense'] =0;
		$dashboard_data['total_expense_by_category'] = [];
		
		//sanitize input
		$user_id = htmlspecialchars(strip_tags($user_id));

		//total income
		$total_income_qry = "select sum(amount) as total_income from income where user_id=:user_id";
		$statement = $this->db_conn->prepare($total_income_qry);
		$statement->bindParam(':user_id', $user_id);
		$statement->execute();
		$total_income = $statement->fetchColumn();
		if($total_income>0)
		{
			$dashboard_data['total_income'] =  $total_income;
		}
		
		//total expense
		$total_expense_qry = "select sum(amount) as total_expense from expense where user_id=:user_id";
		$statement = $this->db_conn->prepare($total_expense_qry);
		$statement->bindParam(':user_id', $user_id);
		$statement->execute();
		$total_expense =  $statement->fetchColumn();		
		if($total_expense>0)
		{
			$dashboard_data['total_expense'] = $total_expense;	
		}

		//total expense by category
		$total_expense_by_category_qry = "select sum(amount) as total_expense, ec.expense_category
		                                  from expense e 
									      join expense_categories ec on ec.expense_category_id=e.category_id
									      where user_id=:user_id
									      group by ec.expense_category";
		$statement = $this->db_conn->prepare($total_expense_by_category_qry);
		$statement->bindParam(':user_id', $user_id);
		$statement->execute();
		$total_expense_by_category =  $statement->fetchAll(PDO::FETCH_ASSOC);		
		if(count($total_expense_by_category)>0)
		{
			$dashboard_data['total_expense_by_category'] = $total_expense_by_category;	
		}

		//total income by category
		$total_income_by_category_qry = "select sum(amount) as total_income, ic.income_category 
									     from income i 
									     join income_categories ic on ic.income_category_id=i.category_id
									     where user_id=:user_id
									     group by ic.income_category";
		$statement = $this->db_conn->prepare($total_income_by_category_qry);
		$statement->bindParam(':user_id', $user_id);
		$statement->execute();
		$total_income_by_category =  $statement->fetchAll(PDO::FETCH_ASSOC);		
		if(count($total_income_by_category)>0)
		{
			$dashboard_data['total_income_by_category'] = $total_income_by_category;	
		}
 		$dashboard_data['budget_expense_spent'] = $this->getBudgetExpenseSpent($user_id,$month,$year);
		return $dashboard_data;
	}


	public function getBudgetExpenseSpent($user_id,$month, $year)
	{
		//sanitize input
		$user_id = htmlspecialchars(strip_tags($user_id));
		$month = htmlspecialchars(strip_tags($month));
		$year = htmlspecialchars(strip_tags($year));

		$start_date = $year . '-' . $month . '-' . '01';
		$end_date = date('Y-m-t',strtotime($start_date));
		$total_days = $this->dateDiffInDays($start_date, $end_date);
		$total_months = $this->dateDiffInMonths($start_date, $end_date);
		if($total_months==0) //given start and end date ends in same month
		{
			$total_months = 1;
		}

		$budget_expense_spent_data = [];
		$budget_expense_spent_data['total_budget'] = 0;
		$budget_expense_spent_data['total_income'] = 0;
		$budget_expense_spent_data['total_expense'] = 0;
		$budget_expense_spent_data['user_budget'] = [];
		$budget_expense_spent_data['budget_expense_spent_date'] = ['start_date' => $start_date,
																   'end_date'=> $end_date,
																   'total_days'=>$total_days,
																   'total_months'=>$total_months ];
 
		$budget_expense_spent_data['budget_expense_by_category'] = [];
	
 		$budget = new Budget($this->db_conn);
		$total_budget = $budget->getTotalBudget($user_id);
		if($total_budget==0)
		{
			return $budget_expense_spent_data;
		}
		$budget_expense_spent_data['total_budget'] = ($total_budget * $total_months);

		$income = new Income($this->db_conn);
		$total_income = $income->getTotalIncome($user_id, $start_date, $end_date);
		$budget_expense_spent_data['total_income'] = $total_income;

		$user_budget = $budget->getAllForUser($user_id);
		$budget_expense_spent_data['user_budget'] = $user_budget;
 		
 		$expense = new Expense($this->db_conn);
 		$user_expenses = $expense->getUserExpenses($user_id, $start_date, $end_date);

		foreach ($user_expenses as $key => $expense) {
			$percentage_spent = 0;

			$total_expense = $expense['amount']; //assume expense spent once

			if($expense['recurring_cost_type_id'] != 1)
			{
				$end_date_of_the_month = date('Y-m-t',strtotime($expense['expense_date']));
				$total_expense_days = $this->dateDiffInDays($expense['expense_date'], $end_date_of_the_month);

				// recurring_cost_type_id --> 1 =  once
				// recurring_cost_type_id --> 2 =  daily
				// recurring_cost_type_id --> 3 =  weekly
 
				if($expense['recurring_cost_type_id']==2) //calculate total days expense
				{
	 				$total_expense = $expense['amount'] * $total_expense_days;
	 			}
				else if($expense['recurring_cost_type_id']==3) //calculate total weeks expense
				{
					$total_weeks = abs(round(($total_expense_days / 7)));
					if($total_weeks == 0)
					{
						$total_weeks = 1;
					}
					$total_expense = $expense['amount'] * $total_weeks;
				}
			}
			$user_expenses[$key]['total_expense'] = $total_expense;
			$budget_expense_spent_data['total_expense'] += $total_expense;
		}
 
		$budget_expense_spent_data['budget_expense_by_category'] = $user_expenses;
		return $budget_expense_spent_data;
	}


	

	public function dateDiffInDays($date1, $date2)  
	{ 
	    // Calulating the difference in timestamps 
	    $diff = strtotime($date2) - strtotime($date1); 
	      
	    // 1 day = 24 hours 
	    // 24 * 60 * 60 = 86400 seconds 
	    return (abs(round($diff / 86400)) + 1); 
	}

	public function dateDiffInMonths($date1, $date2)
	{
			$ts1 = strtotime($date1);
			$ts2 = strtotime($date2);

			$year1 = date('Y', $ts1);
			$year2 = date('Y', $ts2);

			$month1 = date('m', $ts1);
			$month2 = date('m', $ts2);

			return (($year2 - $year1) * 12) + ($month2 - $month1);
	}


 
 

	 

}