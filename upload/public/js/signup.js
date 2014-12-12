$().ready(function() {

	$("#submitbutton.signupform").bind("click", checkDuplicateRegistrationPublisher);
	$("#submitbutton.customersignupform").bind("click", checkDuplicateRegistrationCustomer);
});
	
function checkDuplicateRegistrationPublisher() {
		
		$("#submitbutton").prop("disabled", true);
	
		var email = $("#Email").val();
		var login = $("#user_login").val();
		
		if (!email || !login) {
			$("#submitbutton").prop("disabled", false);
			$('#signupform').submit();
			return false;
		}
		
		  $.getJSON( "/signup/checkpublisherduplicate?email=" + email + "&login=" + login, function( data ) {
			  if (data.result == 'success') {
				  $('#signupform').submit();
			  } else {
				  $("#cdn_form_dynamic_msg").html(data.message).show();
				  $("#submitbutton").prop("disabled", false);
			  }
			  
		  });
		  return false;
}

function checkDuplicateRegistrationCustomer() {
	
	$("#submitbutton").prop("disabled", true);
	
	var email = $("#email").val();
	var login = $("#user_login").val();
	
	if (!email || !login) {
		$("#submitbutton").prop("disabled", false);
		$('#customersignupform').submit();
		return false;
	}

	  $.getJSON( "/signup/checkcustomerduplicate?email=" + email + "&login=" + login, function( data ) {
		  if (data.result == 'success') {
			  $('#customersignupform').submit();
		  } else {
			  $("#cdn_form_dynamic_msg").html(data.message).show();
			  $("#submitbutton").prop("disabled", false);
		  }
		  
	  });
	  return false;
}
