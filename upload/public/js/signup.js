$().ready(function() {

	$("#submitbutton").click( function () {

		var formId = $("form.form-horizontal").attr('id');
		
		var emailId = formId == 'customersignupform' ? 'email' : 'Email';
		
		$("#submitbutton").prop("disabled", true);
	
		var email = $("#" + emailId).val();
		var login = $("#user_login").val();
		
		if (!email || !login) {
			$("#submitbutton").prop("disabled", false);
			$('#' + formId).submit();
			return false;
		}
		
		  $.getJSON( "/signup/checkduplicate?email=" + email + "&login=" + login, function( data ) {
			  if (data.result == 'success') {
				  $('#' + formId).submit();
			  } else {
				  $("#cdn_form_dynamic_msg").html(data.message).show();
				  $("#submitbutton").prop("disabled", false);
			  }
			  
		  });
		  return false;
});

});
	


