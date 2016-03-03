<?php

/*
 * USAGE:
 * 
 * Call this script through SSH:
 * 
 * ./logfile_rollup.php start_date end_date
 * 
 * eg: php ./logfile_rollup.php 03.01.2016 03.02.2016
 * 
 * It will return the stats in JSON format which you can 
 * process with a unified logger that aggregates the 
 * statistics from all NginAd instances.
 */

/*
 * ##########################################################
 * 
 * WE NEED THIS DATA TO PASS THE DSP UNIT TEST
 * 

Won auctions

Sum of all won prices (CPM)
 */

$base_path = @realpath(dirname(__FILE__));

chdir($base_path);

$win_notices_log_dir_prefix = '../logs/dsp/partner_name_logs/win_notices';

$dates = array();

if (!isset($argv[1])):
	die("no date to fetch");
endif;

$start_date = $argv[1];

if (isset($argv[2])):
	$end_date = $argv[2];
	$sdate = strtotime(str_replace(".", "/", $start_date));	
	$edate = strtotime(str_replace(".", "/", $end_date));
	
	for ($i = $sdate; $i < $edate; $i += 18400):
		$dates[date("m.d.Y", $i)] = true;
	endfor;
	
	$dates[date("m.d.Y", $edate)] = true;
	
else:

	$sdate = strtotime(str_replace(".", "/", $start_date));
	$dates[date("m.d.Y", $sdate)] = true;

endif;

foreach ($dates as $date_to_fetch => $val):
	
	$dates[$date_to_fetch] = get_totals_for_date($date_to_fetch);

endforeach;

echo json_encode($dates);
exit;

function get_totals_for_date($date_to_fetch) {
	
	global $win_notices_log_dir_prefix;
	
	$win_notices_log_dir		= $win_notices_log_dir_prefix . "/" . $date_to_fetch;
	
	$won_auctions = 0;
	$sum_bids_won_cpm_cost_net = 0;
	$sum_bids_won_cpm_cost_px = 0;
	$sum_bids_won_cpm = 0;
	
	try {
		$Directory = new RecursiveDirectoryIterator($win_notices_log_dir);
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+\.log$/i', RecursiveRegexIterator::GET_MATCH);
		foreach($Regex as $name => $object){
			// echo "PROCESSING: $name\n";
			add_notice_logs(
				$name,
				$won_auctions,
				$sum_bids_won_cpm_cost_net,
				$sum_bids_won_cpm_cost_px,
				$sum_bids_won_cpm
			);
		}
	} catch (Exception $e) {
	
	}
	
	return (object)array(
		"won_auctions"				=>$won_auctions,
		"sum_bid_prices_net"		=>$sum_bids_won_cpm_cost_net,
		"sum_bid_prices_px"			=>$sum_bids_won_cpm_cost_px,
		"sum_won_prices"			=>$sum_bids_won_cpm,
		"sum_bid_prices_cpm_net"	=>$sum_bids_won_cpm_cost_net / 1000,
		"sum_bid_prices_cpm_px"		=>$sum_bids_won_cpm_cost_px / 1000,
		"sum_won_prices_cpm"		=>$sum_bids_won_cpm / 1000,
	);
	
	/*
	echo "____________ TEST SUMMARY ____________\n\n";
	echo "Won auctions: " . $won_auctions . "\n";
	echo "Sum of all won prices net: " . $sum_bids_won_cpm_cost_net . "\n";
	echo "Sum of all won prices private exchange net: " . $sum_bids_won_cpm_cost_px . "\n";
	echo "Sum of all won prices: " . $sum_bids_won_cpm . "\n";
	echo "Sum of all won prices net CPM: " . $sum_bids_won_cpm_cost_net / 1000 . "\n";
	echo "Sum of all won prices private exchange net CPM: " . $ssum_bids_won_cpm_cost_px / 1000 . "\n";
	echo "Sum of all won prices CPM: " . $sum_bids_won_cpm / 1000 . "\n";
	*/
}

function add_notice_logs(
		$win_notices_log_file,
		&$won_auctions,
		&$sum_bids_won_cpm_cost_net,
		&$sum_bids_won_cpm_cost_px,
		&$sum_bids_won_cpm
		) {
	
	$lines = file($win_notices_log_file);
	
	foreach ($lines as $line):
	
		$line = trim($line);
		
	// 03-02-2016 15:07:44,request_id:cdnp.none56d7723fab1d38.31695590,zone_id:7,buyerid:58,netprc:0.9999,px_price:1.2499,winbid:1.3888,tld:server.nginad.com
		
		$parts = explode(',', $line);
		if (count($parts) < 8) continue;
		
		$date = $parts[0];
		$request_data = split_item($parts[1]);
		$zone_data = split_item($parts[2]);
		$buyer_data = split_item($parts[3]);
		$net_price_data = split_item($parts[4]);
		$private_exchange_bid_data = split_item($parts[5]);
		$win_bid_data = split_item($parts[6]);
		$tld_data = split_item($parts[7]);
	
		$won_auctions++;
		$sum_bids_won_cpm += floatval($win_bid_data[1]);
		$sum_bids_won_cpm_cost_px += floatval($private_exchange_bid_data[1]);
		$sum_bids_won_cpm_cost_net += floatval($net_price_data[1]);
		
	endforeach;

}

function split_item($item) {
	
	$parts = explode(':', $item);
	if (count($parts) == 2) {
		return array($parts[0], $parts[1]);
	}
	return array($item,'');
}