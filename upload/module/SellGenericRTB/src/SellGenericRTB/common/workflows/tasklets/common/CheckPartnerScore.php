<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace sellrtb\workflows\tasklets\common;

class CheckPartnerScore {
	
	public static function execute(&$Logger, &$Workflow, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo) {
		
		$result = false;
	
		$RTBPingerList = $AuctionPopo->SelectedPingerList;
	
		$AuctionPopo->SelectedPingerList = array();
	
		/*
		 * When we have multiple high bids
		*/
	
		/*
		* Determine the high partner score
		*/
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
		
			self::getHighBidPartnerScore($Logger, $AuctionPopo, $RTBPingerList[$y]);
			
		endfor;
		
		/*
		 * Filter out pings who's partner score is less than the highest
		 */
		
		for ($y = 0; $y < count($RTBPingerList); $y++):
		
			if (self::isAuctionWinnerCandidate($Logger, $AuctionPopo, $RTBPingerList[$y])):
			
				/*
				 * Those RTBPingers that still have at least 1 bid get added
				* to the selected pingers list in the POPO
				*/
				$AuctionPopo->SelectedPingerList[] = $RTBPingerList[$y];
					
				$result = true;
			
			endif;
			
		endfor;
		
		return $result;
	
	}
	
	private static function getHighBidPartnerScore(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \pinger\RTBPinger $RTBPinger) {
	
		if ($RTBPinger->partner_quality_score >= $AuctionPopo->highest_partner_score):
			
			$AuctionPopo->highest_partner_score = $RTBPinger->partner_quality_score;
		
			return true;
			
		endif;
		
		return false;
	
	}
	
	private static function isAuctionWinnerCandidate(&$Logger, \sellrtb\workflows\tasklets\popo\AuctionPopo &$AuctionPopo, \pinger\RTBPinger $RTBPinger) {
	
		if ($RTBPinger->partner_quality_score >= $AuctionPopo->highest_partner_score):

			return true;
			
		endif;
		
		return false;
	
	}
}
