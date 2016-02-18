<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace rtbbuyfidelity;

abstract class RtbBuyFidelityLogger extends \rtbbuy\RtbBuyLogger
{
	
	// logging settings
	public $setting_debug 					= false;
	public $setting_log 					= false;
	public $setting_min_log 				= true;
	public $setting_only_log_bids 			= true;
	public $setting_log_to_screen 			= false;
	public $setting_log_file_location 		= "logs/loopbackpartner/";
	
    public $log = array();
    public $min_log = array();
	
    public function output_min_log() {

        $log_file_dir =  $this->setting_log_file_location . date('m.d.Y');

        if (!file_exists($log_file_dir)):
            mkdir($log_file_dir, 0777, true);
        endif;
        $fh = fopen($log_file_dir . "/" . date('g_A') . '.log', "a");

        $real_ip = isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER["REMOTE_ADDR"];

        $log_header = "\n\n";
        $log_header.= 'BID REQUEST : ' . date('m-d-Y H:i:s') . " : FROM : " . $real_ip . "\n";

        fwrite($fh, $log_header);

        if ($this->setting_log_to_screen):

            echo $log_header;

        endif;

        foreach ($this->min_log as $log_line):

            fwrite($fh, $log_line . "\n");

            if ($this->setting_log_to_screen):

                echo $log_line . "\n";

            endif;

        endforeach;

        fclose($fh);

    }

    public function output_log() {
    	if ($this->setting_log):
    		$this->output_log_full();
    	endif;
    	if ($this->setting_min_log):
    		$this->output_min_log();
    	endif;
    }
    
    public function output_auction_results($bid_request_id, $nobid = false, $bidprice = 0, $exception = "") {
    	
    	$log_file_dir = "logs/fidelity_logs/auction/" . date('m.d.Y');
    	
    	if (!file_exists($log_file_dir)):
    		mkdir($log_file_dir, 0777, true);
    	endif;
    	
    	$fh = fopen($log_file_dir . "/" . date('g_A') . '.log', "a");
    	
    	$bid = $nobid ? "0" : "1";
    	$nobid = $nobid ? "1" : "0";
    	
    	$auction_log = date('m-d-Y H:i:s') . ",request_id:" . $bid_request_id
    					. ",bid:" . $bid . ",nobid:" . $nobid  . ",exception:" . $exception
    					.  ",price:" . $bidprice . "\n";
    	
    	fwrite($fh, $auction_log);
    	
    	fclose($fh);
    }
    
    public function output_log_full() {

        $log_file_dir =  $this->setting_log_file_location . date('m.d.Y');

        if (!file_exists($log_file_dir)):
            mkdir($log_file_dir, 0777, true);
        endif;

        $fh = fopen($log_file_dir . "/" . date('g_A') . '.log', "a");

        $real_ip = isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER["REMOTE_ADDR"];

        $log_header = "\n\n";
        $log_header.= "----------------------------------------------------------------\n";
        $log_header.= date('m-d-Y H:i:s') . " ------- NEW BID REQUEST " . $this->rtb_provider . " ---------\n";
        $log_header.= "FROM IP ADDRESS: " . $real_ip . "\n";
        $log_header.= "----------------------------------------------------------------\n";

        fwrite($fh, $log_header);

        if ($this->setting_log_to_screen):

            echo $log_header;

        endif;

        foreach ($this->log as $log_line):

            fwrite($fh, $log_line . "\n");

            if ($this->setting_log_to_screen):

                echo $log_line . "\n";

            endif;

        endforeach;

        fclose($fh);

    }
}
