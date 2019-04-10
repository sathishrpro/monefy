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
	    		 	for(i in data.user_transactions) {
	    		 		var transaction = data.user_transactions[i];
	    		 		var markup = "<tr><td><b>" + transaction.trans_date + "</b></td><td><b>" + transaction.category 
	    		 								   + "</b></td><td>" +  transaction.trans_type + "</td><td>"
	    		 								   +  transaction.amount  + "</b></td><td>" + transaction.recurring_cost_type + "</td></tr>";
            			$("table tbody").append(markup);
	    		 	}
	    		 }
	    	},
	    	error: function (jqXhr, textStatus, errorMessage) {
	        $('#errorMsg').text('Error: ' + errorMessage);
	    	}
	});
}

function dashboardPageReady(){
	
	var datas = {user_id:user.user_id};
	$.ajax('api/reports/dashboard.php', 
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