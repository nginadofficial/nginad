<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace GenericBuysidePartner\Controller;

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
    	
    	\buygenericbuysidepartner\GenericBuysidePartnerInit::init();
    	
        $real_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

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
        	$GenericBuysidePartnerBid = new \buygenericbuysidepartner\GenericBuysidePartnerBid($config, $rtb_seat_id, $response_seat_id);
        	$GenericBuysidePartnerBid->is_local_request = false;
        	$validated = $GenericBuysidePartnerBid->parse_incoming_request();
        	if ($validated === true):
	        	$request_id = $GenericBuysidePartnerBid->RtbBidRequest->id;
	        	$GenericBuysidePartnerBid->process_business_logic();
        	endif;
        	$GenericBuysidePartnerBid->convert_ads_to_bid_response();
        	$GenericBuysidePartnerBid->build_outgoing_bid_response();
        	$GenericBuysidePartnerBid->send_bid_response();
        	if ($GenericBuysidePartnerBid->had_bid_response == true || \buygenericbuysidepartner\GenericBuysidePartnerLogger::get_instance()->setting_only_log_bids == false):
            	\buygenericbuysidepartner\GenericBuysidePartnerLogger::get_instance()->output_log();
        	endif;

        } catch (Exception $e) {
            \buygenericbuysidepartner\GenericBuysidePartnerLogger::get_instance()->log[] = "BID EXCEPTION: ID: " . $request_id . " MESSAGE: " . $e->getMessage();
            header("Content-type: application/json");
            echo '{"seatbid":[{"bid":[{"price":0}]}],"nbr":2}';
        }
        
        if (\buygenericbuysidepartner\GenericBuysidePartnerLogger::get_instance()->setting_only_log_bids == false):
        	\buygenericbuysidepartner\GenericBuysidePartnerLogger::get_instance()->output_log();
        endif;
        
        exit;
    }


}
