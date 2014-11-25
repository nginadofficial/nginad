<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace rtbsellv22;
use rtbsell\RtbSellLogger;

class RtbSellV22Logger extends RtbSellLogger
{
	
	// logging settings
	public $setting_debug 					= false;
	public $setting_log 					= false;
	public $setting_min_log 				= true;
	public $setting_only_log_bids 			= true;
	public $setting_log_to_screen 			= false;
	public $setting_log_file_location 		= "logs/sellside_rtb/";
	
    public $log = array();
    public $min_log = array();
	
	protected $rtb_provider = "sellside_rtb";

	// singleton
	private static $_instance;
	
	public static function get_instance() {
		if (self::$_instance == null):
			self::$_instance = new RtbSellV22Logger();
		endif;
		return self::$_instance;	
	}
	
    public function output_min_log() {

        $log_file_dir =  $this->setting_log_file_location . date('m.d.Y');

        if (!file_exists($log_file_dir)):
            mkdir($log_file_dir, 0777, true);
        endif;
        $fh = fopen($log_file_dir . "/" . date('g_A') . '.log', "a");

        $real_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

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
    
    public function output_log_full() {

        $log_file_dir =  $this->setting_log_file_location . date('m.d.Y');

        if (!file_exists($log_file_dir)):
            mkdir($log_file_dir, 0777, true);
        endif;

        $fh = fopen($log_file_dir . "/" . date('g_A') . '.log', "a");

        $real_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

        $log_header = "\n\n";

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
