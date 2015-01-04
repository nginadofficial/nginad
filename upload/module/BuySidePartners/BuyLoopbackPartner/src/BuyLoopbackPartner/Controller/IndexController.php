<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace BuyLoopbackPartner\Controller;

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
    	
    	\buyloopbackpartner\LoopbackPartnerInit::init();
    	
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
        	$LoopbackPartnerBid = new \buyloopbackpartner\LoopbackPartnerBid($config, $rtb_seat_id, $response_seat_id);
        	$LoopbackPartnerBid->is_local_request = false;
        	$validated = $LoopbackPartnerBid->parse_incoming_request();
        	if ($validated === true):
	        	$request_id = $LoopbackPartnerBid->RtbBidRequest->id;
	        	$LoopbackPartnerBid->process_business_logic();
        	endif;
        	$LoopbackPartnerBid->convert_ads_to_bid_response();
        	$LoopbackPartnerBid->build_outgoing_bid_response();
        	$LoopbackPartnerBid->send_bid_response();
        	if ($LoopbackPartnerBid->had_bid_response == true || \buyloopbackpartner\LoopbackPartnerLogger::get_instance()->setting_only_log_bids == false):
            	\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->output_log();
        	endif;

        } catch (Exception $e) {
            \buyloopbackpartner\LoopbackPartnerLogger::get_instance()->log[] = "BID EXCEPTION: ID: " . $request_id . " MESSAGE: " . $e->getMessage();
            header("Content-type: application/json");
            echo '{"seatbid":[{"bid":[{"price":0}]}],"nbr":2}';
        }
        if (\buyloopbackpartner\LoopbackPartnerLogger::get_instance()->setting_only_log_bids == false):
            \buyloopbackpartner\LoopbackPartnerLogger::get_instance()->output_log();
        endif;

        exit;
    }


}
