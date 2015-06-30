var datatable = null;

// Invocation code  (Publisher/website/zone)
function ShowChooserSsp () {

	$('#myModalLabel').html("SSP RTB Publisher Inventory Feeds");
	
	$('#InvocationCodeModal').modal('show');
	
	if (datatable === null) {
		datatable = $('#example').dataTable( {
			// "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
			"ajax": "/directory/ssp",
			"columns": [
				{ "data": " " },
				{ "data": "Site ID" },
				{ "data": "Domain Name" },
				{ "data": "Daily Impressions" },
				{ "data": "Average CPM" },
				{ "data": "Floor" },
				{ "data": "Exchange" }
			]
		} );
	} else {
		$('#example').dataTable().api().clear();
		$('#example').dataTable().api().ajax.url( '/directory/ssp' ).load();
	}
	
	
	
	
	/*
	$.post("/publisher/zone/" + domain_id + "/generatetag", { ad_id: ad_id }, function( data ) {
		$('#adtag_progress_bar').css("display","none");
		$('#adtag').html(data.data.tag);
		$('#adtag').css("display","block");
	},'json');
	*/

}
