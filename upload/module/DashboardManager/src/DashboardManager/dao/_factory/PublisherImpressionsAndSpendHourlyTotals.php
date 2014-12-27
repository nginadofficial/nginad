<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\Feature;

class PublisherImpressionsAndSpendHourlyTotals extends \_factory\CachedTableRead
{

	static protected $instance = null;
	protected $network_loss_rate_list = array();
	protected $config;
	
	public static function get_instance($config) {

		if (self::$instance == null):
			self::$instance = new \_factory\PublisherImpressionsAndSpendHourlyTotals();
			self::$instance->config = $config;
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'PublisherImpressionsAndSpendHourlyTotals';
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
        	$select->order('PublisherAdZoneID');

        }
        	);

    	    foreach ($resultSet as $obj):
    	    
	    	    $publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getNetworkLossCorrectionRateFromPublisherAdZone($this->config, $obj->PublisherAdZoneID, $this->network_loss_rate_list);
	    	    
	    	    if ($publisher_impressions_network_loss_rate > 0):
	    	     
		    	    $obj->TotalRequests = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->TotalRequests);
		    	    $obj->TotalImpressions = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->TotalImpressions);
		    	    $obj->TotalRevenue = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj->TotalRevenue);
		    	    
	    	    endif;

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
        		$select->order('PublisherAdZoneID');

        	}
    	);

    	    foreach ($resultSet as $obj):

	    	    $publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getNetworkLossCorrectionRateFromPublisherAdZone($this->config, $obj->PublisherAdZoneID, $this->network_loss_rate_list);
	    	    
	    	    if ($publisher_impressions_network_loss_rate > 0):
	    	     
		    	    $obj->TotalRequests = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->TotalRequests);
		    	    $obj->TotalImpressions = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->TotalImpressions);
		    	    $obj->TotalRevenue = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj->TotalRevenue);
		    	    
	    	    endif;
    	    
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

};
