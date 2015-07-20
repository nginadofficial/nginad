function switchImpressionType() {
	
	var impType = $("#ImpressionType").val();
	
	if (impType == 'video') {
		
		$(".novideo").hide();
		$(".nobanner").show();
		$("#passbackTag").html("Passback VAST XML (VAST URL also OK)");
		$("label[for=Height]").html('Video Height');
		$("label[for=Width]").html('Video Width');
		
	} else {
		
		$(".novideo").show();
		$(".nobanner").hide();
		$("#passbackTag").html("Passback Ad Tag");
		$("label[for=Height]").html("Ad Height");
		$("label[for=Width]").html("Ad Width");
	}
	
}
