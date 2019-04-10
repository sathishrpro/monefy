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
						   'I' as trans_type,
						   'N/A' as recurring_cost_type
						   from income i
						   join income_categories ic on ic.income_category_id=i.category_id 
						   where user_id=:user_id
						   union
						   select e.expense_id as id,
						   e.amount,
						   ec.expense_category as category,
						   e.expense_date as trans_date,
						   e.created_at,
						   'E' as trans_type,
						   recurring_cost_type
				 		   from expense e
				  		   join expense_categories ec on ec.expense_category_id=e.category_id
				  		   join recurring_costs_type rct on rct.recurring_cost_type_id=e.recurring_cost_type_id
				  		   where user_id=:user_id) 
				  		   transactions ";

  		if(!empty($filter['trans_start_date']) && !empty($filter['trans_end_date']))
		{
			$filter['trans_start_date'] = htmlspecialchars(strip_tags($filter['trans_start_date']));
			$filter['trans_end_date'] = htmlspecialchars(strip_tags($filter['trans_end_date']));
			$qry .= " where trans_date between '"  . $filter['trans_start_date'] . "' and '" . $filter['trans_end_date'] . "'";
		}

		if(!empty($filter['category']))
		{
			$filter['category'] = htmlspecialchars(strip_tags($filter['category']));
			$qry .= " category =" . $filter['category'];
		}

		$qry .= " order by created_at desc";
  
		$statement = $this->db_conn->prepare($qry);

		//sanitize input
		$user_id = htmlspecialchars(strip_tags($user_id));
 		$statement->bindParam(':user_id', $user_id);
 		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getDashboardData($user_id, $start_date,$end_date)
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
 		$dashboard_data['budget_expense_spent'] = $this->getBudgetExpenseSpent($user_id,$start_date,$end_date);
		return $dashboard_data;
	}


	public function getBudgetExpenseSpent($user_id,$start_date, $end_date)
	{
		//sanitize input
		$user_id = htmlspecialchars(strip_tags($user_id));
		$start_date = htmlspecialchars(strip_tags($start_date));
		$end_date = htmlspecialchars(strip_tags($end_date));

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
		$user_budget_by_categories = [];
		foreach ($user_budget as $key => $ub) {
			$user_budget_by_categories[$ub['expense_category']] = $ub['amount'];
		}
 		$budget_expense_spent_data['user_budget'] = $user_budget_by_categories;


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

  		$user_expenses_by_month_year = [];
		foreach ($user_expenses as $key => $uexp) {
			$exp_month_year = $this->getMonthYearFromDate($uexp['expense_date']);
			$user_expenses_by_month_year[$exp_month_year][] = $uexp;
		} 

		$user_expenses_by_month_year_category = [];
		foreach ($user_expenses_by_month_year as $key => $uexp_monyr) {

			$user_expense_by_category = [];
			foreach ($uexp_monyr as $uexp_monyr_key => $uexp) {
				$category = $uexp['expense_category'];
				if(!isset($user_expense_by_category[$category]))
				{
					$user_expense_by_category[$category] = 0;
				}
				$user_expense_by_category[$category] +=   $uexp['total_expense'] ;
			}
			$user_expenses_by_month_year_category[$key] = $user_expense_by_category;
		}

		$user_budget_expenses = [];
		foreach ($user_expenses_by_month_year_category as $monyr_key => $uexp_monyr) {
			$user_budget_expense_by_category = [];
			foreach ($uexp_monyr as $category_key => $uexp_by_category) {
				$budget_amount = 0;

				if(array_key_exists($category_key, $user_budget_by_categories))
				{
					$budget_amount = $user_budget_by_categories[$category_key];
				}
				$percentage_spent = 100;

				if($budget_amount >0)
				{
					$percentage_spent = round(($uexp_by_category/$budget_amount) * 100);
				}

				$user_budget_expense_by_category[$category_key] = ['total_expense' => $uexp_by_category,
																	'budget_amount'=> $budget_amount,
																	'percentage_spent' => $percentage_spent 
																  ];
			}
			$user_budget_expenses[$monyr_key] = $user_budget_expense_by_category;
		}
 
		$budget_expense_spent_data['user_budget_expenses'] = $user_budget_expenses;
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

			return ((($year2 - $year1) * 12) + ($month2 - $month1))+1;
	}


	public function monthYearFromTwoDates($date1, $date2)
	{
		$output = [];
		$time   = strtotime($date1);
		$last   = date('M-Y', strtotime($date2));

		do {
		    $month = date('M-Y', $time);
 		    $output[] = $month; 
		    $time = strtotime('+1 month', $time);
		} while ($month != $last);
		return $output;
	}

	public function getMonthYearFromDate($date)
	{
		return date('M-Y', strtotime($date));
	}


 
 

	 

}