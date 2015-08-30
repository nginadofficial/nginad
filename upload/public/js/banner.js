var storedVastTagXML = "";
var storedVastTagURL = "";

var iabSizes = '<select id="iabsize" name="iabsize" onchange="formSelectSize(this);">'
    			+ '<option selected="selected" value="">[ SELECT A SIZE ]</option>'
    			+ '<option value="468x60">IAB Full Banner (468 x 60)</option>'
    			+ '<option value="120x600">IAB Skyscraper (120 x 600)</option>'
    			+ '<option value="728x90">IAB Leaderboard (728 x 90)</option>'
    			+ '<option value="120x90">IAB Button 1 (120 x 90)</option>'
    			+ '<option value="120x60">IAB Button 2 (120 x 60)</option>'
    			+ '<option value="234x60">IAB Half Banner (234 x 60)</option>'
    			+ '<option value="88x31">IAB Micro Bar (88 x 31)</option>'
    			+ '<option value="125x125">IAB Square Button (125 x 125)</option>'
    			+ '<option value="120x240">IAB Vertical Banner (120 x 240)</option>'
    			+ '<option value="180x150">IAB Rectangle (180 x 150)</option>'
    			+ '<option value="300x250">IAB Medium Rectangle (300 x 250)</option>'
    			+ '<option value="336x280">IAB Large Rectangle (336 x 280)</option>'
    			+ '<option value="240x400">IAB Vertical Rectangle (240 x 400)</option>'
    			+ '<option value="250x250">IAB Square Pop-up (250 x 250)</option>'
    			+ '<option value="160x600">IAB Wide Skyscraper (160 x 600)</option>'
    			+ '<option value="720x300">IAB Pop-Under (720 x 300)</option>'
    			+ '<option value="300x100">IAB 3:1 Rectangle (300 x 100)</option>'
    			+ '<option value="-">Custom</option>'
    			+ '</select>';

var mobilePhoneSizes = '<select id="iabsize" name="iabsize" onchange="formSelectSize(this);">'
				+ '<option selected="selected" value="">[ SELECT A SIZE ]</option>'
				+ '<option value="320x50">Mobile Phone Banner (320 x 50)</option>'
				+ '<option value="300x50">Mobile Phone Thin Banner (300 x 50)</option>'
				+ '<option value="300x250">Mobile Phone Medium Rectangle (300 x 250)</option>'
				+ '<option value="320x480">Mobile Phone Full Screen (320 x 480)</option>'
				+ '<option value="300x480">Mobile Phone Thin Full Screen (300 x 480)</option>'
    			+ '<option value="-">Custom</option>'
    			+ '</select>';

var mobileTabletSizes = '<select id="iabsize" name="iabsize" onchange="formSelectSize(this);">'
				+ '<option selected="selected" value="">[ SELECT A SIZE ]</option>'
				+ '<option value="728x90">Mobile Tablet Leaderboard (728 x 90)</option>'
				+ '<option value="300x250">Mobile Tablet Medium Rectangle (300 x 250)</option>'
				+ '<option value="300x50">Mobile Tablet Banner (300 x 50)</option>'
				+ '<option value="728x1024">Mobile Tablet Full Screen (728 x 1024)</option>'
				+ '<option value="-">Custom</option>'
				+ '</select>';

function formSelectSize (elem) {

		if($(elem).val() == "" || $(elem).val() == "-") {
			$("#width").val("");
			$("#height").val("");
		}
		var hw = $(elem).val().split("x");

		if (hw.length == 2) {
			$("#width").val(hw[0]);
			$("#height").val(hw[1]);
		}

}

function switchMobile (elem) {

	var mobileType = $(elem).val();

	if (mobileType == 2) {
		$("#size-select").html(mobileTabletSizes);
	} else if (mobileType > 0) {
		$("#size-select").html(mobilePhoneSizes);
	} else {
		$("#size-select").html(iabSizes);
	}
}

function switchInsertionOrderType() {
	
	var adCampaignType = $("#adcampaigntype :selected").text();

}

function updateImageAd(setDefault) {
	var defaultImg = "http://www.iab.net/media/image/728x90.gif";
	var defaultHref = "http://www.example.com/landing-page.html";
	
	var imageAdImgUrl = $("#imageurl").val().trim();
	var imageAdHrefUrl = $("#landingpageurl").val().trim();
	
	if (!imageAdImgUrl && setDefault) {
		imageAdImgUrl = defaultImg;
		$("#imageurl").val(imageAdImgUrl);
	}
	
	if (!imageAdHrefUrl && setDefault) {
		imageAdHrefUrl = defaultHref;
		$("#landingpageurl").val(imageAdHrefUrl);
	}
	
	$('#adtag').prop('readonly', true);
	
	var imageadhtml = '<a href="' + imageAdHrefUrl + '">'
					+ '<img border="0" src="' + imageAdImgUrl + '"></a>';
	$("#adtag").val(imageadhtml);
}

function switchImpressionType(adtagtype) {
	
	if (!adtagtype) adtagtype = "xml";
	
	var impType = $("#ImpressionType").val();
	
	var defaultAdTagInput = '<textarea  class="banner-txtarea"  style="height: 200px; width: 500px;" id="adtag" name="adtag" rows="6"></textarea>';
	var variantAdTagInput = '<input class="input-xxlarge" type="text" id="adtag" name="adtag" />';

	var adtagval = $("#adtag").val();
	
	if (adtagval.indexOf("http") != 0) {
		storedVastTagXML = adtagval;
	} else {
		storedVastTagURL = adtagval;
	}
	
	if (impType == 'video') {
		
		var tagTextXML = 'Video VAST XML <br/> <span class="hlp">'
			+ '<small><i>If you have a VAST <a target="_blank" style="color: blue; text-decoration: underline;"  href="http://ad3.liverail.com/?LR_PUBLISHER_ID=1331&LR_CAMPAIGN_ID=229&LR_SCHEMA=vast2">tag URL</a> from LiveRail or another exchange (<a target="_blank" style="color: blue; text-decoration: underline;"  href="http://www.iab.net/iab_products_and_industry_services/508676/digitalvideo/vast/vast_xml_samples">IAB Examples</a>),<br />just copy/paste the XML into the text area below</small>'
			+ '<br /><small><i>You can use a VPAID SWF if you have one in the VAST - <a style="color: blue; text-decoration: underline;" target="_blank" href="http://support.brightcove.com/en/video-cloud/docs/developing-vpaid-swfs#vast">FAQ on VPAID</a></i></small>'
			+ '</span>';
		
		var tagTextURL = 'Video VAST URL <br/> <span class="hlp">'
			+ '<small><i>If you have a VAST tag URL from LiveRail or another exchange (<a target="_blank" style="color: blue; text-decoration: underline;"  href="http://www.iab.net/iab_products_and_industry_services/508676/digitalvideo/vast/vast_xml_samples">IAB Examples</a>),<br />just copy/paste the URL into the text area below</small>'
			+ '<br /><small style="cursor: text;"><i>eg. <span style="color: blue;">http://ad3.liverail.com/?LR_PUBLISHER_ID=1331&LR_CAMPAIGN_ID=229&LR_SCHEMA=vast2</span></i></small>'
			+ '</span>';

		if (adtagtype == "url") {
			
			$("label[for=adtag]").html(tagTextURL);
			$("#adtagwrapper").html(variantAdTagInput);
			$("#adtag").val(storedVastTagURL);
		} else {

			$("label[for=adtag]").html(tagTextXML);
			$("#adtagwrapper").html(defaultAdTagInput);
			$("#adtag").val(storedVastTagXML);
			$("#adtag").css("height", "350px").css("width", "500px");
		}

		$('#adtag').prop('readonly', false);
		$(".imagead").hide();
		$(".novideo").hide();
		$(".nobanner").show();
		$("#update-button").val("Update Video");
		$("#create-button").val("Create New Video");
		 
		
	} else {
		
		$("#adtagwrapper").html(defaultAdTagInput);
		$("#adtag").val(storedVastTagXML);
		
		$(".novideo").show();
		$(".nobanner").hide();

		if (impType == 'image') {
			
			updateImageAd(true);
			
			$(".imagead").show();
		} else {
			$('#adtag').prop('readonly', false);
			$(".imagead").hide();
		}
		
		$("label[for=adtag]").html("Ad Tag");
		
		$("#update-button").val("Update Banner");
		$("#create-button").val("Create New Banner");
		
		$("#adtag").css("height", "200px").css("width", "500px"); 
		
	}
}

$().ready(function() {
	
	$("#imageurl, #landingpageurl").keyup(function() {
		updateImageAd(false);
	});

});