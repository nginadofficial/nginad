<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;

class HeaderBiddingPage extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\HeaderBiddingPage();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'HeaderBiddingPage';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->initialize();
    }

    public function get_row($params = null) {
        // http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $obj_list = array();

        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params) {
        	foreach ($params as $name => $value):
        	$select->where(
        			$select->where->equalTo($name, $value)
        	);
        	endforeach;
        	$select->limit(1, 0);
        	$select->order('HeaderBiddingPageID');

        }
        	);

    	    foreach ($resultSet as $obj):
    	         return $obj;
    	    endforeach;

        	return null;
    }

    public function get($params = null) {
        	// http://files.zend.com/help/Zend-Framework/zend.db.select.html

        $obj_list = array();

    	$resultSet = $this->select(function (\Zend\Db\Sql\Select $select) use ($params) {
        		foreach ($params as $name => $value):
        		$select->where(
        				$select->where->equalTo($name, $value)
        		);
        		endforeach;
        		//$select->limit(10, 0);
        		$select->order('HeaderBiddingPageID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveHeaderBiddingPage(\model\HeaderBiddingPage $HeaderBiddingPage) {
    	$data = array(
    			'PublisherWebsiteID'                    => $HeaderBiddingPage->PublisherWebsiteID,
    	        'PageName'                       		=> $HeaderBiddingPage->PageName,
    	        'JSHeaderFileUnqName'             		=> $HeaderBiddingPage->JSHeaderFileUnqName,
    			'DateCreated'                        	=> $HeaderBiddingPage->DateCreated
    	);
    	$header_bidding_page_id = (int)$HeaderBiddingPage->HeaderBiddingPageID;
    	if ($header_bidding_page_id === 0): 
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('HeaderBiddingPageID' => $header_bidding_page_id));
    		return $header_bidding_page_id;
    	endif;
    }

    public function deleteHeaderBiddingPage($header_bidding_page_id) {
        $this->delete(array('HeaderBiddingAdUnitID' => $header_bidding_page_id));
    }

    public function deleteHeaderBiddingPageByPublisherAdZoneID($publisher_page_id) {
    	$this->delete(array('PublisherAdZoneID' => $publisher_ad_zone_id));
    }
};