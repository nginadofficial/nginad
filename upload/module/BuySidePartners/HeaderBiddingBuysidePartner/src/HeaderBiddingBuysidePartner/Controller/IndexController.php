<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace HeaderBiddingBuysidePartner\Controller;

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
    	
    	\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerInit::init();
    	
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
        	$HeaderBiddingBuysidePartnerBid = new \buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerBid($config, $rtb_seat_id, $response_seat_id);
        	$HeaderBiddingBuysidePartnerBid->is_local_request = false;
        	$validated = $HeaderBiddingBuysidePartnerBid->parse_incoming_request();
        	if ($validated === true):
	        	$request_id = $HeaderBiddingBuysidePartnerBid->RtbBidRequest->id;
	        	$HeaderBiddingBuysidePartnerBid->process_business_logic();
        	endif;
        	$HeaderBiddingBuysidePartnerBid->convert_ads_to_bid_response();
        	$HeaderBiddingBuysidePartnerBid->build_outgoing_bid_response();
        	$HeaderBiddingBuysidePartnerBid->send_bid_response();
        	if ($HeaderBiddingBuysidePartnerBid->had_bid_response == true || \buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerLogger::get_instance()->setting_only_log_bids == false):
            	\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerLogger::get_instance()->output_log();
        	endif;

        } catch (Exception $e) {
        	
        	$error_response = array('error'=>array('entry'=>$e->getMessage()));
        	\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerLogger::get_instance()->log[] = "BID EXCEPTION: ID: " . $request_id . " MESSAGE: " . $e->getMessage();
        	 
        	header("Content-type: application/x-javascript");
        	echo $this->javascript_callback . '(' . json_encode($error_response) . ')';
        	
        }
        
        if (\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerLogger::get_instance()->setting_only_log_bids == false):
        	\buyheaderbiddingbuysidepartner\HeaderBiddingBuysidePartnerLogger::get_instance()->output_log();
        endif;
        
        exit;
    }


}
