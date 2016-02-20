var PREBID_TIMEOUT = 850;
	
/* 1. Register bidder tag Ids */

	var adUnits = __ADUNITS__;

    var nginadtag = nginadtag || {};
    nginadtag.cmd = nginadtag.cmd || [];

    /* pbjs.initAdserver will be called either when all bids are back, or
       when the timeout is reached.
    */
    function initAdserver() {
        if (pbjs.initAdserverSet) return;
        //load tag library here
        loadNginAdTag();
        pbjs.initAdserverSet = true;
    };
    // Load GPT when timeout is reached.
    setTimeout(initAdserver, PREBID_TIMEOUT);

    var pbjs = pbjs || {};
    pbjs.que = pbjs.que || [];

    // Load the Prebid Javascript Library Async. We recommend loading it immediately after
    // the initAdserver() and setTimeout functions.
    (function() {
        var d = document, pbs = d.createElement("script"), pro = d.location.protocal;
        pbs.type = "text/javascript";
        pbs.src = '/js/prebid/nginad.prebid.js';
        var target = document.getElementsByTagName("head")[0];
        target.insertBefore(pbs, target.firstChild);
    })();


    pbjs.que.push(function(){

    //add the adUnits
    pbjs.addAdUnits(adUnits);

     /* Request bids for the added ad units. If adUnits or adUnitCodes are
           not specified, the function will request bids for all added ad units.
    */
    pbjs.requestBids({

            bidsBackHandler: function(bidResponses) {
                initAdserver();

            }
    });
  
    /* 2. Configure Ad Server Targeting    */
    
    pbjs.bidderSettings = {
        standard: {
            adserverTargeting: [{
                key: "hb_bidder",
                val: function(bidResponse) {
                    return bidResponse.bidderCode;
                }
            }, {
                key: "hb_adid",
                val: function(bidResponse) {
                    return bidResponse.adId;
                }
            }, {
                key: "hb_pb",
                val: function(bidResponse) {
                    return bidResponse.pbMg;
                }
            }, {
                key: "hb_nginad_bidder_id",
                val: function(bidResponse) {
                    return bidResponse.nginadBidderId;
                }
            }, {
                key: "hb_nginad_pub_id",
                val: function(bidResponse) {
                    return bidResponse.nginadPubId;
                }
            }, {
                key: "hb_nginad_zone_height",
                val: function(bidResponse) {
                    return bidResponse.nginadZoneHeight;
                }
            }, {
                key: "hb_nginad_zone_width",
                val: function(bidResponse) {
                    return bidResponse.nginadZoneWidth;
                }
            }, {
                key: "hb_nginad_zone_tld",
                val: function(bidResponse) {
                    return bidResponse.nginadZoneTld;
                }
            }
            ]
        }
    };

});
    

    function loadAdTagDivWithTag(divId, queryStringParams, bidderProvider) {
    	
	    var nginads = document.createElement('script');
	    nginads.async = true;
	    nginads.type = 'text/javascript';
	    var useSSL = 'https:' == document.location.protocol;
	    var protocol = useSSL ? 'https:' : 'http:';
	    
	    var ord = Math.random()*10000000000000000;
	    var scriptSrc = protocol + '//__NGINAD_SERVER_DOMAIN__/ad/nginad.js?hb=true&' + queryStringParams + 'cb=' + ord;
	    nginads.src = scriptSrc;

	    var node = document.getElementById(divId);
	    
	    if (node && node != 'undefined') {
	    	node.appendChild(nginads);
	    }
    }
    
    function loadAdTagAdUrl(divId, adUrl, bidderProvider) {
    	
    	/*
    	 * Some header bidding ad exchanges populate the 
    	 * adUrl with tracking scripts such as:
    	 * Pubmatic's adUrl is a tracking script
    	 * 
    	 * However, others like AppNexus send the actual ad creative 
    	 * in HTML via the adUrl, so we can't append it to 
    	 * all header bid responses globally since the behavior 
    	 * is inconsistent from exchange to exchange.
    	 * 
    	 * For now lets just do Pubmatic's and see if more
    	 * ad exchanges send back a tracking script rather than the 
    	 * Ad HTML in the future.
    	 */
    	
    	if (bidderProvider == 'nginad') {
    		// NginAd has no such tracker/cookie match
    		return;
    	} else if (bidderProvider == 'pubmatic') {
    		// Pubmatic has a valid tracking script in adUrl
    		;
    	} else {
    		/*
    		 * Other bidders may or may not have valid trackers
    		 * in adUrl, but AppNexus does not, so we need to 
    		 * pass on those for now.
    		 */ 
    		return;
    	}
    	
	    var rtbWinnerAdUrl = document.createElement('script');
	    rtbWinnerAdUrl.async = true;
	    rtbWinnerAdUrl.type = 'text/javascript';

	    rtbWinnerAdUrl.src = adUrl;

	    var node = document.getElementById(divId);
	    
	    if (node && node != 'undefined') {
	    	node.appendChild(rtbWinnerAdUrl);
	    }
    }
    
    function loadNginAdTag() {

    	var divIdList = [];
    	var divIdHadAuctionResultList = [];
    	
    	for (var k in adUnits) {
    		
    		var adUnit = adUnits[k];
    		divIdList.push(adUnit.code);
    		
    	}
    	
		var adTargetInfo = pbjs.getAdserverTargeting();
	
		// DEBUG
		// console.log(adTargetInfo); 
		
		if (!adTargetInfo) {
			// console.log("No Bids Available");
			return;
		}
		
		var queryParams = 	'hb=true&';
		
		var hasAd = false;
		
		for (var k in adTargetInfo) {

			var bidderType = 'nginad'; // default
			
			var adUrl = '';
			
			if (adTargetInfo.hasOwnProperty(k)) {
				var qp = '';
				for (var v in adTargetInfo[k]) {
					
					if (v == 'hb_bidder') {
						bidderType 	= adTargetInfo[k][v];
					} 
					
					if (v == 'adUrl') {
						adUrl 		= adTargetInfo[k][v];
					} 

					if (v == 'hb_nginad_pub_id') {
						qp += "pzoneid=" + adTargetInfo[k][v] + "&";
					} else if (v == 'hb_nginad_zone_height') {
						qp += "height=" + adTargetInfo[k][v] + "&";
					} else if (v == 'hb_nginad_zone_width') {
						qp += "width=" + adTargetInfo[k][v] + "&";
					} else if (v == 'hb_nginad_zone_tld') {
						qp += "tld=" + adTargetInfo[k][v] + "&";
					} else if (v == 'adUrl') {
						adUrl = adTargetInfo[k][v];
					} else {
						qp += v + "=" + adTargetInfo[k][v] + "&";
					}
				}
			}	
			
			var divId = k;
			
			divIdHadAuctionResultList.push(divId);

			loadAdTagDivWithTag(divId, qp, bidderType);
			if (adUrl) {
				loadAdTagAdUrl(divId, adUrl, bidderType);
			}
		}
		
		for (var k in adUnits) {
			
			var auctionDivId = adUnits[k].code;
			
			if (divIdHadAuctionResultList.indexOf(auctionDivId) == -1) {
				
				var bids = adUnits[k].bids;
				if (bids.length >= 1) {
					
					var firstBid = bids[0];
					var qp = 'houseAds=true&';
					qp += "hb_nginad_bidder_id=" + firstBid.params.hb_nginad_bidder_id + "&";
					qp += "pzoneid=" + firstBid.params.hb_nginad_pub_id + "&";
					qp += "height=" + firstBid.params.hb_nginad_zone_height + "&";
					qp += "width=" + firstBid.params.hb_nginad_zone_width + "&";
					qp += "tld=" + firstBid.params.hb_nginad_zone_tld + "&";

					loadAdTagDivWithTag(divIdList[k], qp, 'nginad', '');
				}
			}
		}
}
    