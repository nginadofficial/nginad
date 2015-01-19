var currentBanners = {};
var completeBannerList = {};

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

function switchPublisherAdZoneType() {
	
	var publisherAdZoneType = $("#PublisherAdZoneTypeID :selected").text();

	if (publisherAdZoneType == "Contract") {
		$("#passback-cg, #floor-cg").hide();
		getBannersDataAndPopulate();
	} else {
		$("#passback-cg, #floor-cg").show();
		$("#banner-picker").hide();
	}
}

function populateBanners() {
	var height 			= $("#height").val();
	var width 			= $("#width").val();
	if (height && width) {
		populateBannersSelect();
	} else {
		$("#weight-box").hide();
		$("#banner-picker").html("");
		$("#banner-picker").hide();
	}
}

function hasCurrentBanner(currentBannerArray, currentBanner) {

	for (var key in currentBannerArray) {
		if (currentBannerArray[key] == currentBanner) {
			return true;
		}
	}

	return false;
}

function registerBanners() {

	currentBanners = "";
	var comma = "";

	$("#linkedbanners option:selected").each(
			function() {
				currentBanners += comma + $(this).val();
				comma = ",";
			}
	);

}

function getBannersDataAndPopulate() {
	
	$("#PublisherAdZoneTypeID").prop("disabled", true);

	var campaignId 		= "";
	var previewParam 	= "false";
	var zoneId 			= $("input[name='PublisherAdZoneID']").val();
	var domainId 		= $("input[name='PublisherWebsiteID']").val();
	
	var height 			= $("#Height").val();
	var width 			= $("#Width").val();

	if (!height || !width) {
		alert("Select zone dimensions first, choose a height and width.");
		$("#banner-picker").hide();
		$("#PublisherAdZoneTypeID").val(1);
		$("#PublisherAdZoneTypeID").prop("disabled", false);
		$("#passback-cg, #floor-cg").show();
		return false;
	}

	var zoneParam = "";
	
	if (zoneId && zoneId != 'undefined') {
		zoneParam = zoneId;
	}
	
	if (!completeBannerList[width+'x'+height]) {
		$.get("/publisher/zone/" + domainId + "/editlinkedbanner/" + zoneParam + "?height=" + height + "&width=" + width + "&is_preview=" + previewParam, function( data ) {
			$("#zone_no_btn").attr("disabled",false);
			if(data.success == false) {
				alert("There are no demand banners that match this publisher ad zone");
				$("#banner-picker").hide();
				$("#PublisherAdZoneTypeID").val(1);
				$("#PublisherAdZoneTypeID").prop("disabled", false);
				$("#passback-cg, #floor-cg").show();
				return false;
			}
			
			if(data.success == true) {
				
				currentBanners = data.linked_ad_banners;
				completeBannerList[width+'x'+height] = data.complete_banner_list;
				populateBannersSelect(data.complete_banner_list);
			
			}
			
		},'json');
	} else {
		populateBannersSelect(completeBannerList[width+'x'+height]);
	}
}

function populateBannersSelect(complete_banner_list) {
	var selectBannersPre = '<select id="linkedbanners" name="linkedbanners[]"  onchange="registerBanners();" multiple style="min-height: 200px">';
	var selectBanners = "";
	var foundBanner = false;
	var currentBannerArray = currentBanners.split(",");
	var height 			= $("#Height").val();
	var width 			= $("#Width").val();
	
	for (i = 0; i < complete_banner_list.length; i++) {
		
		var current_banner = complete_banner_list[i];
		var ad_name = current_banner.ad_name + " - " + width + "x" + height;
		if (hasCurrentBanner(currentBannerArray, current_banner.banner_id) == true) {
			foundBanner = true;
			selectBanners += '<option value="' + current_banner.banner_id + '" selected="selected">' + ad_name + '</option>';
		} else {
			selectBanners += '<option value="' + current_banner.banner_id + '">' + ad_name + '</option>';
		}
	}
	selectBanners += '</select>';

	var allSelectBanners = selectBannersPre;
	if (foundBanner == false) {
		allSelectBanners += '<option value="" selected="selected">Choose Banners</option>';
	}
	allSelectBanners += selectBanners;
	
	$("#PublisherAdZoneTypeID").prop("disabled", false);
	$("#weight-box").show();
	var header = '<div id="choose-banners">Choose Banners to Associate to this Zone:</div>';
	allSelectBanners = header + allSelectBanners;
	$("#banner-picker").html(allSelectBanners);
	$("#banner-picker").show();
}

$().ready(function() {

	switchPublisherAdZoneType();
});