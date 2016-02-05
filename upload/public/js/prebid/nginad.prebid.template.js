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
            }
            ]
        }
    };

});
    
    function loadNginAdTag() {

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
			
			if (adTargetInfo.hasOwnProperty(k)) {
				var qp = '';
				for (var v in adTargetInfo[k]) {
					qp += v + "=" + adTargetInfo[k][v] + "&";
				}
			}	
			
			qp += 'adTagDiv=' + k + '&';
			
			queryParams += 'divId[]=' + encodeURIComponent(qp) + '&';
			hasAd = true;
		}
		
		if (hasAd == false) {
			queryParams += 'houseAds=true';
		}

	    var nginads = document.createElement('script');
	    nginads.async = true;
	    nginads.type = 'text/javascript';
	    var useSSL = 'https:' == document.location.protocol;
	    var protocol = useSSL ? 'https:' : 'http:';
	    var scriptSrc = protocol + '//__NGINAD_SERVER_DOMAIN__/ad/nginad.js?' + queryParams;
	    nginads.src = scriptSrc;

	    var node = document.getElementsByTagName('script')[0];
	    node.parentNode.insertBefore(nginads, node);
        
}
