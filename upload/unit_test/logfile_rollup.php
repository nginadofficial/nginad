<?php

/*
 * USAGE:
 * 
 * Call this script through SSH:
 * 
 * ./logfile_rollup.php start_date end_date
 * 
 * eg: ./logfile_rollup.php 05.05.2015 05.07.2015
 * 
 * It will return the stats in JSON format which you can 
 * process with a unified logger that aggregates the 
 * statistics from all NginAd instances.
 */

/*
 * ##########################################################
 * 
 * WE NEED THIS DATA TO PASS THE FIDELITY UNIT TEST
 * 
Bids

No bids

Won auctions

Sum of all bid prices (CPM)

Sum of all won prices (CPM)
 */

$base_path = @realpath(dirname(__FILE__));

chdir($base_path);

$auction_log_dir_prefix = '../logs/fidelity_logs/auction';
$win_notices_log_dir_prefix = '../logs/fidelity_logs/win_notices';

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
	
	global $auction_log_dir_prefix, $win_notices_log_dir_prefix;
	
	$auction_log_dir 			= $auction_log_dir_prefix . "/" . $date_to_fetch;
	$win_notices_log_dir		= $win_notices_log_dir_prefix . "/" . $date_to_fetch;
	
	$bids = 0;
	$nobids = 0;
	$won_auctions = 0;
	$sum_total_cpm = 0;
	$sum_bids_won_cpm = 0;

	try {
		$Directory = new RecursiveDirectoryIterator($auction_log_dir);
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+\.log$/i', RecursiveRegexIterator::GET_MATCH);
		foreach($Regex as $name => $object){
			// echo "PROCESSING: $name\n";
			add_auction_logs(
				$name,
				$bids,
				$nobids,
				$won_auctions,
				$sum_total_cpm,
				$sum_bids_won_cpm
			);
		}
	} catch (Exception $e) {
		
	}
	
	try {
		$Directory = new RecursiveDirectoryIterator($win_notices_log_dir);
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+\.log$/i', RecursiveRegexIterator::GET_MATCH);
		foreach($Regex as $name => $object){
			// echo "PROCESSING: $name\n";
			add_notice_logs(
				$name,
				$bids,
				$nobids,
				$won_auctions,
				$sum_total_cpm,
				$sum_bids_won_cpm
			);
		}
	} catch (Exception $e) {
	
	}
	
	return (object)array(
		"bids"					=>$bids,
		"no_bids"				=>$nobids,
		"won_auctions"			=>$won_auctions,
		"sum_bid_prices"		=>$sum_total_cpm,
		"sum_won_prices"		=>$sum_bids_won_cpm,
		"sum_bid_prices_cpm"	=>$sum_total_cpm / 1000,
		"sum_won_prices_cpm"	=>$sum_bids_won_cpm / 1000,
	);
	
	/*
	echo "____________ TEST SUMMARY ____________\n\n";
	echo "Bids: " . $bids . "\n";
	echo "No Bids: " . $nobids . "\n";
	echo "Won auctions: " . $won_auctions . "\n";
	echo "Sum of all bid prices: " . $sum_total_cpm . "\n";
	echo "Sum of all won prices: " . $sum_bids_won_cpm . "\n";
	echo "Sum of all bid prices CPM: " . $sum_total_cpm / 1000 . "\n";
	echo "Sum of all won prices CPM: " . $sum_bids_won_cpm / 1000 . "\n";
	*/
}

function add_auction_logs(
		$auction_log_file,
		&$bids,
		&$nobids,
		&$won_auctions,
		&$sum_total_cpm,
		&$sum_bids_won_cpm
) {

	$lines = file($auction_log_file);

	foreach ($lines as $line):

		$line = trim($line);
	
		// 05-02-2015 18:57:10,request_id:1x2fzxWCrf,bid:1,nobid:0,exception:,price:0.5049
	
		$parts = explode(',', $line);
		if (count($parts) < 6) continue;
	
		$date = $parts[0];
		$request_data = split_item($parts[1]);
		$bid_data = split_item($parts[2]);
		$nobid_data = split_item($parts[3]);
		$exception_data = split_item($parts[4]);
		$price_data = split_item($parts[5]);
	
		$bids += intval($bid_data[1]);
		$nobids += intval($nobid_data[1]);
		$sum_total_cpm += floatval($price_data[1]);

	endforeach;


}

function add_notice_logs(
		$win_notices_log_file,
		&$bids,
		&$nobids,
		&$won_auctions,
		&$sum_total_cpm,
		&$sum_bids_won_cpm
		) {
	
	$lines = file($win_notices_log_file);
	
	foreach ($lines as $line):
	
		$line = trim($line);
		
	// 05-02-2015 18:57:08,request_id:1xir9r8n2n,banner_id:16,buyerid:1111,orgprc:0.5049,winbid:na,tld:not_available
		
		$parts = explode(',', $line);
		if (count($parts) < 7) continue;
		
		$date = $parts[0];
		$request_data = split_item($parts[1]);
		$banner_data = split_item($parts[2]);
		$buyer_data = split_item($parts[3]);
		$org_price_data = split_item($parts[4]);
		$win_bid_data = split_item($parts[5]);
		$tld_data = split_item($parts[6]);
	
		$won_auctions++;
		$sum_bids_won_cpm += floatval($win_bid_data[1]);
		
	endforeach;

}

function split_item($item) {
	
	$parts = explode(':', $item);
	if (count($parts) == 2) {
		return array($parts[0], $parts[1]);
	}
	return array($item,'');
}