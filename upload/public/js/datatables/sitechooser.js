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

function parse_feed_id(raw_feed_data) {
	
	raw_feed_data = unescape(raw_feed_data);
	
	var start = raw_feed_data.indexOf(':');

	if (start === -1) {
		return null;
	}
	
	var feed_id = raw_feed_data.substring(0, start);
	var next_string = raw_feed_data.substring(start + 1);
	
	start = next_string.indexOf(':');

	if (start === -1) {
		return null;
	}
	
	var feed_exchange = next_string.substring(0, start);
	var feed_description = next_string.substring(start + 1);
	
	return {
		"id" 			: feed_id,
		"exchange" 		: feed_exchange,
		"description" 	: feed_description
	};
	
}

function addSspItem(elemId, siteId, labelText) {
	
	var found = false;
	
	var siteIdObj = parse_feed_id(siteId);
	if (siteIdObj === null) return;
	
	$("#" + elemId + " > option").each(function() {

		var siteIdCompareObj = parse_feed_id(this.value);
		
		if (siteIdCompareObj !== null) {
			if ((siteIdCompareObj.id == siteIdObj.id) && siteIdCompareObj.exchange == siteIdObj.exchange) {
				// it's already in the list
				found = true;
				return;
			}
		}
	});
	
	if (found === false) {
		$('#' + elemId).append($('<option>', {
		    value: escape(siteId),
		    text: labelText
		}));
	}
}

function callBackDblClk() {
	$('#rtb-feed-chooser tr').dblclick( function() {
		var nTds = $('td', this);
		
		var selInput = $('input', nTds[0]);
		
		var optionLabel = unescape(selInput.attr('labelname'));
		var optionValue = unescape(selInput.val());
		
		addSspItem(current_rtb_tier, optionValue, optionLabel);
		$('#cls-btn').click();
	} );
}

function removeSspItem(elemId, siteId) {
	var sel = $('#' + elemId);
	sel.find("option[value='" + siteId + "']").remove();
}

function removeSelectedSspItems() {
	$("#ssp-feeds > option:selected").remove();
}

function removeSelectedPcItems() {
	$("#pc-feeds > option:selected").remove();
}

function removeSelectedPxItems() {
	$("#px-feeds > option:selected").remove();
}

// Private Exchange
function showChooserPx () {

	current_rtb_tier = 'px-feeds';
	
	$('#myModalLabel').html("Your Private Exchange Publisher Inventory Feeds");
	
	$('#InvocationCodeModal').modal('show');

	if (datatable === null) {
		datatable = $('#rtb-feed-chooser').dataTable( {
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
		$('#rtb-feed-chooser').dataTable().api().clear().draw();
	}
	
	var params = "";
	
	if (insertion_order_id !== null) {
		params = '?insertion-order-id=' + insertion_order_id;
	}
	
	$('#rtb-feed-chooser').dataTable().api().ajax.url( '/directory/privateexchange' + params ).load(
		function () {
			callBackDblClk();
		}		
	);
}

// Platform Connection
function showChooserPc () {

	current_rtb_tier = 'pc-feeds';
	
	$('#myModalLabel').html("Platform Connection Publisher Inventory Feeds");
	
	$('#InvocationCodeModal').modal('show');

	if (datatable === null) {
		datatable = $('#rtb-feed-chooser').dataTable( {
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
		$('#rtb-feed-chooser').dataTable().api().clear().draw();
	}
	
	var use_date = $('#stats-date-pc').val();
	var re = new RegExp('/', 'g');
	use_date = use_date.replace(re, '-');
	
	var params = 'selected-date=' + escape(use_date);
	
	if (insertion_order_id !== null) {
		params += '&insertion-order-id=' + insertion_order_id;
	}
	
	$('#rtb-feed-chooser').dataTable().api().ajax.url( '/directory/platformconnection?' + params ).load(
		function () {
			callBackDblClk();
		}	
	);

}

// SSP
function showChooserSsp () {

	current_rtb_tier = 'ssp-feeds';
	
	$('#myModalLabel').html("SSP RTB Publisher Inventory Feeds");
	
	$('#InvocationCodeModal').modal('show');

	if (datatable === null) {
		datatable = $('#rtb-feed-chooser').dataTable( {
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
		$('#rtb-feed-chooser').dataTable().api().clear().draw();
	}
	
	var use_date = $('#stats-date-ssp').val();
	var re = new RegExp('/', 'g');
	use_date = use_date.replace(re, '-');
	
	var params = 'selected-date=' + escape(use_date);
	
	if (insertion_order_id !== null) {
		params += '&insertion-order-id=' + insertion_order_id;
	}
	
	$('#rtb-feed-chooser').dataTable().api().ajax.url( '/directory/ssp?' + params ).load(
		function () {
			callBackDblClk();
		}		
	);
}

/*
 * hightlight all form selections before
 * submission
 */
function highlightSelects() {
	$("#pc-feeds > option:not(:selected)").prop('selected', true);
	$("#px-feeds > option:not(:selected)").prop('selected', true);
	$("#ssp-feeds > option:not(:selected)").prop('selected', true);
}
