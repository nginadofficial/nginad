var pageNewID = 0;

function switchImpressionType() {
	
	var impType = $("#ImpressionType").val();
	
	if (impType == 'video') {
		
		var auctionType = $("#AuctionType").val();
		
		if (auctionType != 'rtb') {
			$("#AuctionType").val('rtb');
			switchAuctionType();
		}
		
		$(".novideo").hide();
		$(".nobanner").show();
		$("#passbackTag").html("Passback VAST XML (VAST URL also OK)");
		$("label[for=Height]").html('Video Height');
		$("label[for=Width]").html('Video Width');
		$("#header-bidding-wrapper").hide();
		$("#auction-type-wrapper").hide();
	
	} else {
		
		$(".novideo").show();
		$(".nobanner").hide();
		$("#passbackTag").html("Passback Ad Tag");
		$("label[for=Height]").html("Ad Height");
		$("label[for=Width]").html("Ad Width");
		$("#header-bidding-wrapper").show();
		$("#auction-type-wrapper").show();
	}
	
}

function newPageHeader() {
	
	var newPage = $.trim($("#new-page-header").val());
	
	var exists = false;

	// dedupe
	$("#PageHeaderID option").each(function() {
		
		if ($(this).text().toLowerCase() == newPage.toLowerCase()) {
			exists = true;
		}
	});
	
	if (exists == true) {
		alert('That Page Header ID Already Exists');
		return;
	}

	$("#PageHeaderID").append($('<option>', {
	    value: newPage,
	    text: newPage
	}));
	
	$('#PageHeaderID option').attr('selected','');
	$('#PageHeaderID option[value=' + newPage + ']').attr('selected','selected');
}

function switchAuctionType(auctionType) {
	
	if (!auctionType) {
		auctionType = $("#AuctionType").val();
	} else {
		$("#AuctionType").val(auctionType);
	}
	
	if (auctionType == 'header') {
		
		$("#header-bidding-wrapper").show();
		
	} else {
		
		$("#header-bidding-wrapper").hide();
	}
	
}

function initializeHeaderBiddingAdNetworks() {
	
	var currentHeaderBiddingAdNetworkList = jQuery.parseJSON(current_header_bidding_ad_network_json);

	for (i = 0; i < currentHeaderBiddingAdNetworkList.length; i++) {

		initializeHeaderBiddingAdNetwork(currentHeaderBiddingAdNetworkList[i]);
	    
	}
}

function initializeHeaderBiddingAdNetwork(currentHeaderBiddingAdNetworks) {
	
    var HeaderBiddingAdUnitID 				= currentHeaderBiddingAdNetworks.HeaderBiddingAdUnitID;
    var HeaderBiddingPageID 				= currentHeaderBiddingAdNetworks.HeaderBiddingPageID;
    var PublisherAdZoneID 					= currentHeaderBiddingAdNetworks.PublisherAdZoneID;
    var AdExchange 							= currentHeaderBiddingAdNetworks.AdExchange;
    var DivID 								= currentHeaderBiddingAdNetworks.DivID;
    var Height 								= currentHeaderBiddingAdNetworks.Height;
    var Width 								= currentHeaderBiddingAdNetworks.Width;
    var CustomParams 						= currentHeaderBiddingAdNetworks.CustomParams;
    var AdTag 								= currentHeaderBiddingAdNetworks.AdTag;
    
    var id 									= newHeaderBiddingAdNetwork(null, AdExchange, true);
    
    if (AdTag) {
    	$("#NetworkAdTag" + id).val(AdTag);
    }
    
    if (HeaderBiddingAdUnitID) {
    	$("#HeaderBiddingAdUnitID" + id).val(HeaderBiddingAdUnitID);
    }
    
    if (AdExchange == 'appnexus') {
    	
    	if (CustomParams.placementId) {
    		$("#PlacementID" + id).val(CustomParams.placementId);
    	}
    	
    	if (CustomParams.randomeKey) {
    		$("#RandomKey" + id).val(CustomParams.randomeKey);
    	}
    
    } else if (AdExchange == 'rubicon') {
    	
    	if (CustomParams.rp_account) {
    		$("#RpAccount" + id).val(CustomParams.rp_account);
    	}
    	
    	if (CustomParams.rp_site) {
    		$("#RpSite" + id).val(CustomParams.rp_site);
    	}
    	
    	if (CustomParams.rp_zonesize) {
    		$("#RpZoneSize" + id).val(CustomParams.rp_zonesize);
    	}
    	
    } else if (AdExchange == 'openx') {
    	
    	if (CustomParams.jstag_url) {
    		$("#JsTagUrl" + id).val(CustomParams.jstag_url);
    	}
    	
    	if (CustomParams.pgid) {
    		$("#PgId" + id).val(CustomParams.pgid);
    	}
    	
    	if (CustomParams.unit) {
    		$("#Unit" + id).val(CustomParams.unit);
    	}
    	
    } else if (AdExchange == 'pubmatic') {
    	
    	if (CustomParams.publisherId) {
    		$("#PublisherID" + id).val(CustomParams.publisherId);
    	}
    	
    	if (CustomParams.adSlot) {
    		$("#AdSlot" + id).val(CustomParams.adSlot);
    	}
    	
    } else if (AdExchange == 'criteo') {
    	
    	if (CustomParams.nid) {
    		$("#NID" + id).val(CustomParams.nid);
    	}
    	
    	if (CustomParams.cookiename) {
    		$("#CookieName" + id).val(CustomParams.cookiename);
    	}

    	if (CustomParams.varname) {
    		$("#VarName" + id).val(CustomParams.varname);
    	}
    	
    } else if (AdExchange == 'yieldbot') {
    	
    	if (CustomParams.psn) {
    		$("#Psn" + id).val(CustomParams.psn);
    	}
    	
    	if (CustomParams.slot) {
    		$("#Slot" + id).val(CustomParams.slot);
    	}

    } else if (AdExchange == 'indexexchange') {
    	
    	if (CustomParams.id) {
    		$("#ID" + id).val(CustomParams.id);
    	}
    	
    	if (CustomParams.siteID) {
    		$("#SiteID" + id).val(CustomParams.siteID);
    	}
    	
    	if (CustomParams.tier2SiteID) {
    		$("#Tier2SiteID" + id).val(CustomParams.tier2SiteID);
    	}
    	
    	if (CustomParams.tier3SiteID) {
    		$("#Tier3SiteID" + id).val(CustomParams.tier3SiteID);
    	}
    	
    } else if (AdExchange == 'sovrn') {
    	
    	if (CustomParams.tagid) {
    		$("#TagID" + id).val(CustomParams.tagid);
    	}
    	
    } else if (AdExchange == 'aol') {
    	
    	if (CustomParams.placement) {
    		$("#Placement" + id).val(CustomParams.placement);
    	}
    	
    	if (CustomParams.network) {
    		$("#Network" + id).val(CustomParams.network);
    	}
    	
    	if (CustomParams.sizeId) {
    		$("#SizeID" + id).val(CustomParams.sizeId);
    	}
    	
    	if (CustomParams.alias) {
    		$("#Alias" + id).val(CustomParams.alias);
    	}
    	
    } else if (AdExchange == 'pulsepoint') {
    	
    	if (CustomParams.cf) {
    		$("#Cf" + id).val(CustomParams.cf);
    	}
    	
    	if (CustomParams.cp) {
    		$("#Cp" + id).val(CustomParams.cp);
    	}
    	
    	if (CustomParams.ct) {
    		$("#Ct" + id).val(CustomParams.ct);
    	}
    	
    }
    	
}

function removeHeaderBiddingAdNetwork(id) {
	
	$("#n-header-bidding-id-" + id).remove();
}

function newHeaderBiddingAdNetwork(id, headerBiddingType, skipScroll) {
	
	var newElement = false;
	
	if (!headerBiddingType) {
		headerBiddingType = $("#HeaderBiddingType").val();
	}
	
	var elementHtml = $("#header-bidding-type-" + headerBiddingType.toLowerCase()).html();
	
	if (!id) {
		newElement = true
		id = newHeaderBiddingId();
	}
	
	elementHtml = replaceAll("_N_", id, elementHtml);
	
	elementHtml = '<div class="n-header-bidding-item header-bidding-' + headerBiddingType.toLowerCase() + '" id="n-header-bidding-id-' + id + '">' + elementHtml + '</div>';
	
	$("#HeaderBiddingItems").append(elementHtml);
	
	if (skipScroll != true) {
		if (newElement == true) {
		    $('html, body').animate({
		        scrollTop: $('#n-header-bidding-id-' + id).offset().top - 100
		    }, 2000);
		}
	}
	
	return id;
}

function replaceAll(find, replace, str) {
	  return str.replace(new RegExp(find, 'g'), replace);
}

function newHeaderBiddingId() {
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var length = 16;
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
    return '_NEW_' + result;
}

