var nativePreviewTitleText = 'Learn about this awesome thing';
var nativePreviewDescriptionText = 'Learn all about this awesome story of someone using my product.';
var nativePreviewSponsoredText = 'My Brand';

function initializeNativeAssets() {
	
	if (!native_ad_data_json || native_ad_data_json == "") return;
	
	var nativeAdDataList = jQuery.parseJSON(native_ad_data_json);

	for (i = 0; i < nativeAdDataList.length; i++) {

		initializeAsset(nativeAdDataList[i]);
	    
	}
}

function initializeAsset(nativeAdData) {
	
    var AssetRequired 						= nativeAdData.AssetRequired;
    var DataType 							= nativeAdData.DataType;
    var DataLabel 							= nativeAdData.DataLabel;
    var DataValue 							= nativeAdData.DataValue;
    
    var id 									= newNativeAsset(null, AssetType);
    
    if (DataType) {
    	$("#DataAssetType" + id).val(DataType);
	}
    	
	if (DataLabel) {
		$("#DataAssetLabel" + id).val(DataLabel);
	}
    	
	if (DataValue) {
		$("#DataAssetValue" + id).val(DataValue);
	}
    
    if (AssetRequired && AssetRequired == 1) {
    	$("#DataAssetRequiredContainer" + id + ' .required-on').prop("checked", true);
    } else {
    	$("#DataAssetRequiredContainer" + id + ' .required-off').prop("checked", true);
    }

}

function removeDataElement(id) {
	
	$("#n-data-id-" + id).remove();
}

function newDataElement(id, dataType, div, parentId) {
	
	if (!dataType) {
		dataType = $("#AttributeType").val();
	}
	
	var divHtml = $("#data-template").html();
	
	if (!id) {
		id = newDataId();
	}
	
	divHtml = replaceAll("_N_", id, divHtml);
	
	divHtml = '<div class="n-data native-' + dataType + '" id="n-data-id-' + id + '">' + divHtml + '</div>';
	
	if (!div) {
		$("#native-data").append(divHtml);
	} else {
		div.append(divHtml);
	}
	return id;
}

function replaceAll(find, replace, str) {
	  return str.replace(new RegExp(find, 'g'), replace);
}

function newDataId() {
	var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var length = 16;
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
    return '_NEW_' + result;
}

function scrollPreviewToViewport() {
	var aTop = 180;

	if($(this).scrollTop() >= aTop) {
		$('#floating-preview-pane').css('margin-top', $(this).scrollTop() - aTop);
	}
}

$().ready(function() {
	
	$('#data-title').keyup(function(){
			var txt = $('#data-title').val();
			if (!txt) {
				txt = nativePreviewTitleText;
			}
			$('#native-preview-title').html(txt);
	});
	
	$('#data-description').keyup(function(){
		var txt = $('#data-description').val();
		if (!txt) {
			txt = nativePreviewDescriptionText;
		}
		$('#native-preview-description').html(txt);
	});
	
	$('#data-sponsored').keyup(function(){
		var txt = $('#data-sponsored').val();
		if (!txt) {
			txt = nativePreviewSponsoredText;
		}
		$('#native-preview-brand').html(txt);
	});
	
	$(window).scroll(function(){
		scrollPreviewToViewport();
	});

	scrollPreviewToViewport();
});
	