<?php 
/*
 * This file will comine the statistics from multiple instances of
 * unit_test_dsp/logfile_rollup.php on your server and output the stats
 * in human readable format.
 * 
 * It's purpose is for RTB stats roll-ups from DSPs in order to 
 * do number matching or accounting.
 * 
 * It should be executed by the command line.
 */

/*
 * USAGE:
*
* php ./get_all_stats.php start_date end_date
*
* eg: php ./get_all_stats.php 03.01.2016 03.02.2016
*
* It will return the stats in JSON format which you can
* process with a unified logger that aggregates the
* statistics from all NginAd instances.
*/

// path to unit_test_dsp/logfile_rollup.php on servers

$path_to_logfile_rollup = '/home/nginad/upload/unit_test_dsp/';

// NginAd Server IPs and auth info:

$nginad_server_ips = array(
	"nginad-instance-1.example.com"		=> array(
											"username" => "root", 
											"password" => "p@ssw0rd"
											),

	"nginad-instance-2.example.com"		=> array(
											"username" => "root", 
											"password" => "p@ssw0rd"
											),
		
	"111.222.333.444"					=> array(
											"username" => "root",
											"password" => "p@ssw0rd"
											),
		
);

$dates = array();

if (!isset($argv[1])):
	die("no date to fetch");
endif;

$start_date = $argv[1];
$sdate = strtotime(str_replace(".", "/", $start_date));

$start_date 	= date("m.d.Y", $sdate);
$end_date 		= null;

if (isset($argv[2])):
	$end_date = $argv[2];
	$edate = strtotime(str_replace(".", "/", $end_date));
	$end_date = date("m.d.Y", $edate);
endif;


set_include_path(__DIR__ . '/phpseclib' . PATH_SEPARATOR . get_include_path());
include_once('Net/SSH2.php');

$all_data = array();

foreach ($nginad_server_ips as $nginad_server_ip => $auth_info):

	echo "LOGGING INTO SERVER: " . $nginad_server_ip . "\n\n";

	$json_response = get_json_info_from_server($nginad_server_ip, $auth_info['username'], $auth_info['password'], $start_date, $end_date);

	$bid_data_list = json_decode($json_response, true);
	
	foreach ($bid_data_list as $date_of_data => $bid_data):
		
		var_dump($bid_data);
		
		echo "\n\n";
	
		if (isset($all_data[$date_of_data])):
		
			$all_data[$date_of_data]["won_auctions"] 			+= $bid_data["won_auctions"];
			$all_data[$date_of_data]["sum_bid_prices_net"] 		+= $bid_data["sum_bid_prices_net"];
			$all_data[$date_of_data]["sum_bid_prices_px"] 		+= $bid_data["sum_bid_prices_px"];
			$all_data[$date_of_data]["sum_won_prices"] 			+= $bid_data["sum_won_prices"];
			$all_data[$date_of_data]["sum_bid_prices_cpm_net"] 	+= $bid_data["sum_bid_prices_cpm_net"];
			$all_data[$date_of_data]["sum_bid_prices_cpm_px"] 	+= $bid_data["sum_bid_prices_cpm_px"];
			$all_data[$date_of_data]["sum_won_prices_cpm"] 		+= $bid_data["sum_won_prices_cpm"];
		else:
			
			$all_data[$date_of_data]["won_auctions"] 			= $bid_data["won_auctions"];
			$all_data[$date_of_data]["sum_bid_prices_net"] 		= $bid_data["sum_bid_prices_net"];
			$all_data[$date_of_data]["sum_bid_prices_px"] 		= $bid_data["sum_bid_prices_px"];
			$all_data[$date_of_data]["sum_won_prices"] 			= $bid_data["sum_won_prices"];
			$all_data[$date_of_data]["sum_bid_prices_cpm_net"] 	= $bid_data["sum_bid_prices_cpm_net"];
			$all_data[$date_of_data]["sum_bid_prices_cpm_px"] 	= $bid_data["sum_bid_prices_cpm_px"];
			$all_data[$date_of_data]["sum_won_prices_cpm"] 		= $bid_data["sum_won_prices_cpm"];
		endif;

	endforeach;
	
endforeach;

echo "____________ ALL LOGS SUMMARY ________________________\n\n";

foreach ($all_data as $date_of_data => $data_aggregate):

	echo "____________ LOGS SUMMARY FOR DATE: " .str_replace('.', '/', $date_of_data)  . " ____________\n\n";
	echo "Won auctions: " . number_format($data_aggregate["won_auctions"]) . "\n";
	// echo "Sum of all won net prices: $" . number_format($data_aggregate["sum_bid_prices_net"], 4) . "\n";
	// echo "Sum of all won private exchange net prices: $" . number_format($data_aggregate["sum_bid_prices_px"], 4) . "\n";
	// echo "Sum of all won prices: $" . number_format($data_aggregate["sum_won_prices"], 4) . "\n";
	echo "Sum of all won net prices CPM: $" . number_format($data_aggregate["sum_bid_prices_cpm_net"], 2) . "\n";
	echo "Sum of all won private exchange net prices CPM: $" . number_format($data_aggregate["sum_bid_prices_cpm_px"], 2) . "\n";
	echo "Sum of all won prices CPM: $" . number_format($data_aggregate["sum_won_prices_cpm"], 2) . "\n\n\n";

endforeach;


function get_json_info_from_server($ip_address, $username, $password, $start_date, $end_date = null) {

	global $path_to_logfile_rollup;
	
	$ssh = new Net_SSH2($ip_address);
	if (!$ssh->login($username, $password)):
	    exit('Login Failed');
	endif;
	
	$command = 'php ' . $path_to_logfile_rollup . 'logfile_rollup.php ' . $start_date;
	
	if ($end_date !== null):
		$command.= ' ' . $end_date;
	endif;
	
	$json_response = null; 
	
	try {
		$json_response = $ssh->exec($command);
	} catch (Exception $e) {
		die($e->getMessage());
	}

	return $json_response;
}




