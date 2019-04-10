/*$( window ).load(function() {
  // Run code
  console.log('Window Load');
})*/
 var user = undefined;
$(document).ready(function(){
	user = localStorage.getItem('user');
	if(user){
		// window.location.href = 'dashboard.html';
		user = JSON.parse(user);
	}  

 	if($('#income-page').length){
 		incomePageReady();
 	}
 	if($('#expense-page').length){
 		expensePageReady();
 		
 	}
 	if($('#budget-page').length){
 		budgetPageReady();
 	}
 	if($('#transaction-page').length){
 		transactionPageReady();
 	}
 	if($('#dashboard-page').length){
 		dashboardPageReady();
 	}

 	$('#btnSignout').click(function(){
 		localStorage.clear();
 		window.location.href = "index.html";
 	})
 	
})

$('#btnSignup').click(function(){
	if(validateFormSignup().valid){
		$.ajax('api/users/signup.php', 
		{	
			type:'post',
			data:$( "#formSignup" ).serialize(),
	   	 	dataType: 'json',
		    	timeout: 500,
		    	success: function (data,status,xhr) {
		    		$('#errorMsg').text('Success: ' + data.message);
		    	},
		    	error: function (jqXhr, textStatus, errorMessage) {
		        $('#errorMsg').text('Error: ' + errorMessage);
		    	}
		});
	}
	else{
		$('#errorMsg').text('Error: ' + validateFormSignup().message);
	}
})

$('#btnLogin').click(function(){
	 
	$.ajax('api/users/login.php', 
	{	
		type:'post',
		data:$( "#formSignin" ).serialize(),
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		$('#errorMsg').text('Success: ' + data.message);
	    		localStorage.setItem('user',JSON.stringify(data.user));
	    		window.location.href = 'dashboard.html';
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});
	 
})

function validateFormSignup(){
	var isValid = true;
	var message = '';
	var formValArr = $( "#formSignup" ).serializeArray();
	$.each(formValArr,function(index,value){
		if(!value.value || value.value.length < 1){
			message = 'All Fields are mandatory';
			isValid =  false;
		}
	})
	if(isValid && formValArr[1].value != formValArr[2].value){
		message = "Confirm Password Doesn't Match";
		isValid =  false;
	}
	return {valid:isValid,message:message};
}


function incomePageReady(){
	$.ajax('api/income-categories/read.php', 
	{	
		type:'get',
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.length > 0){
	    		 	for(i in data) {
	    		 	 	$("#categories").append("<option value='"+data[i].id+"'>"+data[i].category+"</option>");
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});

	$('#btnAdd').click(function(){
		var datas = [];
		$.each($("#formIncome").serializeArray(),function(key,val){
			datas.push(val);
		})
		datas.push({name:'user_id',value:user.user_id});
		$.ajax('api/income/create.php', 
		{	
			type:'post',
			data:datas,
	   	 	dataType: 'json',
		    	timeout: 500,
		    	success: function (data,status,xhr) {
	    		  	$('#errorMsg').text(data.message);
	    		  	setTimeout(function(){
	    		  		location.reload();
	    		  	},2000)
		    	},
		    	error: function (jqXhr, textStatus, errorMessage) {
		        $('#errorMsg').text('Error: ' + errorMessage);
		    	}
		});
	})
}

function expensePageReady(){
	$.ajax('api/expense-categories/read.php', 
	{	
		type:'get',
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.length > 0){
	    		 	for(i in data) {
	    		 	 	$("#categories").append("<option value='"+data[i].id+"'>"+data[i].category+"</option>");
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});

	$.ajax('api/recurring-costs/read.php', 
	{	
		type:'get',
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.length > 0){
	    		 	for(i in data) {
	    		 	 	$("#recurring_costs").append("<option value='"+data[i].id+"'>"+data[i].recurring_cost_type+"</option>");
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});

	$('#btnAdd').click(function(){
		var datas = [];
		$.each($("#formExpense").serializeArray(),function(key,val){
			datas.push(val);
		})
		datas.push({name:'user_id',value:user.user_id});
		$.ajax('api/expense/create.php', 
		{	
			type:'post',
			data:datas,
	   	 	dataType: 'json',
		    	timeout: 500,
		    	success: function (data,status,xhr) {
	    		  	$('#errorMsg').text(data.message);
	    		  	// setTimeout(function(){
	    		  	// 	location.reload();
	    		  	// },2000)
		    	},
		    	error: function (jqXhr, textStatus, errorMessage) {
		        $('#errorMsg').text('Error: ' + errorMessage);
		    	}
		});
	})
}

function budgetPageReady(){
	var datas = {user_id:user.user_id};
	$.ajax('api/budget/user_budget_categories.php', 
	{	
		type:'get',
		data:datas,
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.length > 0){
	    		 	for(i in data) {
	    		 	 	$("#categories").append("<option value='"+data[i].expense_category_id+"'>"+data[i].expense_category+"</option>");
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});

	var datas = {user_id:user.user_id};
	$.ajax('api/budget/read.php', 
	{	
		type:'get',
		data:datas,
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.budget && data.budget.length > 0){
	    		 	for(i in data.budget) {
	    		 		var budget = data.budget[i];
	    		 		var markup = "<tr><td><b>" + budget.expense_category + "</b></td><td>" +  budget.amount + "</td></tr>";
            			$("table tbody").append(markup);
	    		 	}
	    		 	var total_budget = data.total_budget;
	    		 	$('#total_budget').append(total_budget);
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});

	$('#btnAdd').click(function(){
		var datas = [];
		$.each($("#formBudget").serializeArray(),function(key,val){
			datas.push(val);
		})
		datas.push({name:'user_id',value:user.user_id});
		$.ajax('api/budget/create.php', 
		{	
			type:'post',
			data:datas,
	   	 	dataType: 'json',
		    	timeout: 500,
		    	success: function (data,status,xhr) {
	    		  	$('#errorMsg').text(data.message);
	    		  	// setTimeout(function(){
	    		  	// 	location.reload();
	    		  	// },2000)
		    	},
		    	error: function (jqXhr, textStatus, errorMessage) {
		        $('#errorMsg').text('Error: ' + errorMessage);
		    	}
		});
	})
}


function transactionPageReady(){
	$.ajax('api/expense-categories/read.php', 
	{	
		type:'get',
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.length > 0){
	    		 	for(i in data) {
	    		 	 	$("#categories").append("<option value='"+data[i].id+"'>"+data[i].category+"</option>");
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});

	var datas = {user_id:user.user_id};
	$.ajax('api/reports/transactions.php', 
	{	
		type:'get',
		data:datas,
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data && data.user_transactions && data.user_transactions.length > 0){
	    		 	$('#totalRecords').text(data.user_transactions.length);
	    		 	for(i in data.user_transactions) {
	    		 		var transaction = data.user_transactions[i];
	    		 		var trans_type = 'Income';
	    		 		if(transaction.trans_type == 'E'){
	    		 			trans_type = 'Expense';
	    		 		}
	    		 		var markup =   `<tr>
	    		 					  	<td><b>` + transaction.trans_date + `</b></td>
	    		 					  	<td><b>` + transaction.category + `</b></td>
 								   		<td>` +  trans_type + `</td>
 								   		<td>`+  transaction.amount  + `</td>
 								   		<td>` + transaction.recurring_cost_type + `</td>
 								   		<td>
 								   			<button class="btn btn-danger" onclick='deleteTransaction("`+transaction.trans_type+`",`+transaction.id+`)'>Delete</button>
 								   		</td></tr>`;
            			$("table tbody").append(markup);
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});
}

function editTransaction(trans_type,trans_id) {
	console.log('Edit'+trans_id);
}

function deleteTransaction(trans_type,trans_id) {

	var datas = {income_id:trans_id};
	var url = 'api/income/delete.php';

	if(trans_type == 'E'){
		datas = {expense_id:trans_id};
		url = 'api/expense/delete.php';
	}

	$.ajax(url, 
	{	
		type:'get',
		data:datas,
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data){
	    		 	 console.log(data);
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});
}

function dashboardPageReady(){
	var dashboard_data = '';
	var datas = {user_id:user.user_id,start_date:'2019-01-01'};
	getDashboardAnalytics(datas);
	$('#btnFilter').click(function(){
		var datas = [];
		$.each($("#formDashboardFilter").serializeArray(),function(key,val){
			datas.push(val);
		})
		datas.push({name:'user_id',value:user.user_id});
		getDashboardAnalytics(datas);
	})
}

function getDashboardAnalytics(datas){

	$.ajax('api/reports/dashboard.php', 
	{	
		type:'get',
		data:datas,
   	 	dataType: 'json',
	    	timeout: 500,
	    	success: function (data,status,xhr) {
	    		 if(data){
	    		 	generate_income_charts(data.dashboard_data);
	    		 	generate_expense_charts(data.dashboard_data);
	    		 	generate_budget_charts(data.dashboard_data);
	    		 	generate_budget_exp_charts(data.dashboard_data);
	    		 	set_budget_spent(data.dashboard_data);
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});
}

	function generate_income_charts(values){
		var incomes = values.total_income_by_category;
		var income_data = [];
		var income_label = [];
		var income_color = [];
		$.each(incomes,function(index,value) {
			income_data.push(value.total_income);
			income_label.push(value.income_category);
			income_color.push(dynamicColors());
		})
		var ctx = document.getElementById('incomeChart').getContext('2d');
		var myPieChart = new Chart(ctx, {
		    type: 'pie',
		    data:  {
			    datasets: [{
			        data: income_data,
			        backgroundColor: income_color,
			    }],
			    labels: income_label
			}
		});



	}
	function generate_expense_charts(values){
		var expenses = values.total_expense_by_category;
		var expense_data = [];
		var expense_label = [];
		var expense_color = [];
		$.each(expenses,function(index,value) {
			expense_data.push(value.total_expense);
			expense_label.push(value.expense_category);
			expense_color.push(dynamicColors());
		})
		var ctx = document.getElementById('expenseChart').getContext('2d');
		var myPieChart = new Chart(ctx, {
		    type: 'pie',
		    data:  {
			    datasets: [{
			        data: expense_data,
			        backgroundColor: expense_color,
			    }],
			    labels: expense_label
			}
		});
	}

	function generate_budget_charts(values){
		var budgets = values.budget_expense_spent.user_budget;//values.budget_expense_spent.user_budget_expenses;
		var budget_data = [];
		var budget_label = [];
		var budget_color = [];
		$.each(budgets,function(index,value) {
			budget_data.push(value);
			budget_label.push(index);
			budget_color.push(dynamicColors());
		})


		var ctx = document.getElementById('budgetChart').getContext('2d');
		var myBarChart = new Chart(ctx, {
		    type: 'bar',
	      	data: {
		        labels: budget_label,
		        datasets: [{
		            label: 'Budget',
		            data: budget_data,
		            backgroundColor: budget_color,
		            borderWidth: 1
		        }]
		    },
		    options: {
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero: true
		                }
		            }]
		        }
		    }
		});
	}

	function generate_budget_exp_charts(values){
		$("table tbody").empty();
		var budgets = values.budget_expense_spent.user_budget_expenses;
		 
		$.each(budgets,function(index,value) {
			$.each(value,function(i,v) {
				 

				var markup =   `<tr>
						  	<td><b>` + index + `</b></td>
						  	<td><b>` + i + `</b></td>
					   		<td>` +  v.budget_amount + `</td>
					   		<td>`+  v.total_expense  + `</td>
					   		<td>` + v.percentage_spent + `%</td>
					   		 </tr>`;
				$("table tbody").append(markup);

			})
		})
	}

	function set_budget_spent(values){
		var budgets = values.budget_expense_spent;
		$('#total_budget').text(budgets.total_budget);
		$('#total_expense').text(budgets.total_expense);
		$('#total_income').text(budgets.total_income);
		var range = budgets.budget_expense_spent_date;
		console.log(range);
		$('#date_range').text(range.start_date+' to '+range.end_date);

		 
	}


	var dynamicColors = function() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
     };