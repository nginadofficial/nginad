<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace FidelityBuysidePartner\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use \Exception;

class IndexController extends AbstractActionController
{

    private $ban_ips = array(
    	//"192.168.",
        //"127.0.0"
    );

    public function indexAction()
    {
        echo "NGINAD<br />\n";

        // Debugging code.
        // print_r($_SESSION);
        exit;
    }

    public function bidAction()
    {
    	
    	\buyfidelitybuysidepartner\FidelityBuysidePartnerInit::init();
    	
        $real_ip = isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER["REMOTE_ADDR"];

        foreach ($this->ban_ips as $ban_ip):
            if (strpos($real_ip, $ban_ip) !== false):
                $this->indexAction();
            endif;
        endforeach;

        $config = $this->getServiceLocator()->get('Config');
        $application_config = $this->getServiceLocator()->get('ApplicationConfig');
        $rtb_seat_id 		= isset($application_config['rtb_seat_id']) ? $application_config['rtb_seat_id'] : null;
        $response_seat_id 	= isset($application_config['response_seat_id']) ? $application_config['response_seat_id'] : null;

        $request_id = "Not Given";
        try {
        	$FidelityBuysidePartnerBid = new \buyfidelitybuysidepartner\FidelityBuysidePartnerBid($config, $rtb_seat_id, $response_seat_id);
        	$FidelityBuysidePartnerBid->is_local_request = false;
        	$validated = $FidelityBuysidePartnerBid->parse_incoming_request();
        	if ($validated === true):
	        	$request_id = $FidelityBuysidePartnerBid->RtbBidRequest->id;
	        	$FidelityBuysidePartnerBid->process_business_logic();
        	endif;
        	
    		/*
    		 * FIDELITY MOD:
    		 * One bid per request only. Multiple bid/seat responses won’t be accepted.
    		 */
        	$FidelityBuysidePartnerBid->fidelity_dedupe_bid_response();
        	
        	$FidelityBuysidePartnerBid->convert_ads_to_bid_response();
        	$FidelityBuysidePartnerBid->build_outgoing_bid_response();
        	
        	if ($FidelityBuysidePartnerBid->had_bid_response === false):
        		/*
        		 * SSP must provide access to a test server that supports the URL parameters testbid=bid and testbid=nobid. 
        		 * In the first case a test bid has to be returned to our test bid request, in the latter case a “no bid” 
        		 * response has to be returned (normally represented by an HTTP return code 204, see OpenRTB specification).
        		 */
        		http_response_code(204);
        		\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->output_auction_results($request_id, true);
        	else:
	        	$FidelityBuysidePartnerBid->send_bid_response();
        		\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->output_auction_results($request_id, false, $FidelityBuysidePartnerBid->RtbBidResponse->RtbBidResponseSeatBidList[0]->RtbBidResponseBidList[0]->price);
        	endif;
        	
        	if ($FidelityBuysidePartnerBid->had_bid_response === true || \buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->setting_only_log_bids == false):
        		\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->output_log();
        	endif;
        } catch (Exception $e) {
        	\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->output_auction_results($request_id, true, 0, $e->getMessage());
            \buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->log[] = "BID EXCEPTION: ID: " . $request_id . " MESSAGE: " . $e->getMessage();
            header("Content-type: application/json");
            echo '{"nbr":2}';
        }
        
        if (\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->setting_only_log_bids == false):
        	\buyfidelitybuysidepartner\FidelityBuysidePartnerLogger::get_instance()->output_log();
        endif;
        
        exit;
    }


}
