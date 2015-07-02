var datatable = null;
var current_rtb_tier = 'ssp-feeds';

function addCheckedBoxes() {
	$('#rtb-feed-chooser .ckssp:checked').each(function() {
		var optionLabel = unescape($(this).attr('labelname'));
		var optionValue = unescape($(this).val());
		addSspItem(current_rtb_tier, optionValue, optionLabel);
	});
	$('#cls-btn').click();
}

function addSspItem(elemId, siteId, labelText) {
	$("#" + elemId + " > option").each(function() {
		if (this.value == siteId) {
			// it's already in the list
			return;
		}
	});
	$('#' + elemId).append($('<option>', {
	    value: siteId,
	    text: labelText
	}));
}

function removeSspItem(elemId, siteId) {
	var sel = $('#' + elemId);
	sel.find("option[value='" + siteId + "']").remove();
}

function removeSelectedSspItems() {
	$("#" + current_rtb_tier + " > option:selected").remove();
}

// Invocation code  (Publisher/website/zone)
function showChooserSsp () {

	current_rtb_tier = 'ssp-feeds';
	
	$('#myModalLabel').html("SSP RTB Publisher Inventory Feeds");
	
	$('#InvocationCodeModal').modal('show');

	if (datatable === null) {
		datatable = $('#rtb-feed-chooser').dataTable( {
			"ajax": "/directory/ssp",
			"columns": [
				{ "data": " " },
				{ "data": "Site ID" },
				{ "data": "Domain" },
				{ "data": "Name" },
				{ "data": "IAB Cat" },
				{ "data": "Daily Imps" },
				{ "data": "Average CPM" },
				{ "data": "Floor" },
				{ "data": "Exchange" }
			]
		} );
	} else {
		$('#rtb-feed-chooser').dataTable().api().clear();
		$('#rtb-feed-chooser').dataTable().api().ajax.url( '/directory/ssp' ).load();
	}
}
