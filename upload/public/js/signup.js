$().ready(function() {

	$("#signupform").bind("submit", checkDuplicateRegistrationPublisher);
	$("#customersignupform").bind("submit", checkDuplicateRegistrationCustomer);
});
	
function checkDuplicateRegistrationPublisher() {
		
		var email = $("#Email").val();
		var login = $("#user_login").val();
		
		if (!email || !login) {
			return false;
		}
		
		  $.getJSON( "/signup/checkpublisherduplicate?email=" + email + "&login=" + login, function( data ) {
			  if (data.result == 'success') {
				  $("#signupform").unbind("submit", checkDuplicateRegistrationPublisher);
				  $("#signupform").submit();
			  } else {
				  alert(data.message);
				  return false;
			  }
			  
		  });
		  return false;
}

function checkDuplicateRegistrationCustomer() {
	
	var email = $("#Email").val();
	var login = $("#user_login").val();
	
	if (!email || !login) {
		return false;
	}
	
	  $.getJSON( "/signup/checkcustomerduplicate?email=" + email + "&login=" + login, function( data ) {
		  if (data.result == 'success') {
			  $("#customersignupform").unbind("submit", checkDuplicateRegistrationCustomer);
			  $("#customersignupform").submit();
		  } else {
			  alert(data.message);
			  return false;
		  }
		  
	  });
	  return false;
}
