var PREBID_TIMEOUT = 700;
	
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

    //register a callback handler
    pbjs.addCallback('adUnitBidsBack', function(adUnitCode){
        console.log('ad unit bids back for : ' + adUnitCode);
    });

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

	    console.log('divId: ');
	    console.log(divId);
	    
	    var node = document.getElementById(divId);
	    
	    console.log('node: ');
	    console.log(node);
	    
	    if (node && node != 'undefined') {
	    	node.appendChild(nginads);
	    	console.log('successfully loaded adtag in divId: ' + divId);
	    } else {
	    	console.log('failed at loading adtag in divId: ' + divId);
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
	
		console.log(adTargetInfo);
		
		if (!adTargetInfo) {
			console.log("No Bids Available");
			return;
		}
		
		var queryParams = 	'hb=true&';
		
		var hasAd = false;
		
		for (var k in adTargetInfo) {
			
			console.log(k);
			
			var bidderType = 'nginad'; // default
			
			if (adTargetInfo.hasOwnProperty(k)) {
				var qp = '';
				for (var v in adTargetInfo[k]) {
					
					if (v == 'hb_bidder') {
						bidderType = adTargetInfo[k][v];
					} 

					if (v == 'hb_nginad_pub_id') {
						qp += "pzoneid=" + adTargetInfo[k][v] + "&";
					} else if (v == 'hb_nginad_zone_height') {
						qp += "height=" + adTargetInfo[k][v] + "&";
					} else if (v == 'hb_nginad_zone_width') {
						qp += "width=" + adTargetInfo[k][v] + "&";
					} else if (v == 'hb_nginad_zone_tld') {
						qp += "tld=" + adTargetInfo[k][v] + "&";
					} else {
						qp += v + "=" + adTargetInfo[k][v] + "&";
					}
				}
			}	
			
			var divId = k;
			
			if (divIdList.indexOf(divId) != -1) {
				 divIdHadAuctionResultList.push(divId);
			}
			
			loadAdTagDivWithTag(divId, qp, bidderType);
			
		}
		
		for (var k in adUnits) {
			
			var auctionDivId = adUnits[k].code;
			
			console.log(auctionDivId);
			
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
					
					loadAdTagDivWithTag(divIdList[k], qp, 'nginad');
				}
			}
		}
}
