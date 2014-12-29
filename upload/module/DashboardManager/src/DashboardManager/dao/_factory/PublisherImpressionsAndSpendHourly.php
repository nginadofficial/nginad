<?php

/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Sql\Sql;
use Zend\Db\Metadata\Metadata;

class PublisherImpressionsAndSpendHourly extends \_factory\CachedTableRead {

    static protected $instance = null;
    static $visibleAdminFiealds = array();
    static $visibleUserFiealds = array();
    protected $network_loss_rate_list = array();
	protected $config;
	
    public static function get_instance($config) {

        if (self::$instance == null):
            self::$instance = new \_factory\PublisherImpressionsAndSpendHourly();
        	self::$instance->config = $config;
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'PublisherImpressionsAndSpendHourly';
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());

        $this->adminFields = array_merge($this->adminFields, array(
        	'PublisherInfoID',
            'GrossECPM',
            'GrossRevenue',
        	'PublisherName',
        	'DateCreated'
        ));
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
            $select->order(array('MDYH', 'PublisherAdZoneID'));
        }
        );

        foreach ($resultSet as $obj):
        
	        $publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getNetworkLossCorrectionRateFromPublisherAdZone($this->config, $obj->PublisherAdZoneID, $this->network_loss_rate_list);
	         
	        if ($publisher_impressions_network_loss_rate > 0):
	        
		        $obj->Requests = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->Requests);
		        $obj->Impressions = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->Impressions);
		        $obj->Revenue = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj->Revenue);
		        $obj->GrossRevenue = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj->GrossRevenue);
		         
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
            $select->order(array('MDYH', 'PublisherAdZoneID'));
        }
        );

        foreach ($resultSet as $obj):
        
	        $publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getNetworkLossCorrectionRateFromPublisherAdZone($this->config, $obj->PublisherAdZoneID, $this->network_loss_rate_list);
	        
	        if ($publisher_impressions_network_loss_rate > 0):
		        
		        $obj->Requests = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->Requests);
		        $obj->Impressions = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj->Impressions);
		        $obj->Revenue = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj->Revenue);
		        $obj->GrossRevenue = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj->GrossRevenue);
		         
	        endif;
	        
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTimeCustom($where_params = null, $is_admin = 0) {
    
    	$obj_list = array();
    
    
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array(
    			'MDYH' => new \Zend\Db\Sql\Expression('MAX(MDYH)'),
    			'PublisherAdZoneID',
    			'PublisherName',
    			'PublisherInfoID',
    			'AdName',
    			'Requests' => new \Zend\Db\Sql\Expression('SUM(Requests)'),
    			'Impressions' => new \Zend\Db\Sql\Expression('SUM(Impressions)'),
    			'eCPM' => new \Zend\Db\Sql\Expression("IFNULL(AVG(eCPM), '')"),
    			'GrossECPM' => new \Zend\Db\Sql\Expression("IFNULL(AVG(GrossECPM), '')"),
    			'FillRate' => new \Zend\Db\Sql\Expression("CONCAT(ROUND(AVG(REPLACE(FillRate, '%', '')), 2), '%')"),
    			'Revenue' => new \Zend\Db\Sql\Expression('SUM(Revenue)'),
    			'GrossRevenue' => new \Zend\Db\Sql\Expression('SUM(GrossRevenue)'),
    			'DateCreated' => new \Zend\Db\Sql\Expression('MAX(DateCreated)')
    	));
    	$select->from('PublisherImpressionsAndSpendHourly');
    	if (!empty($where_params['DateCreatedGreater'])):
    	$select->where(
    			$select->where->greaterThanOrEqualTo('DateCreated', $where_params['DateCreatedGreater'])
    	);
    	endif;
    
    	if (!empty($where_params['DateCreatedLower'])):
    	$select->where(
    			$select->where->lessThanOrEqualTo('DateCreated', $where_params['DateCreatedLower'])
    	);
    	endif;
    	
    	foreach ($where_params as $name => $value):
	    	if ($name != 'DateCreatedLower' && $name != 'DateCreatedGreater'):
		    	$select->where(
		    			$select->where->equalTo($name, $value)
		    	);
	    	endif;
    	endforeach;
    	
    	$select->group('PublisherAdZoneID');
    	$select->order('PublisherAdZoneID');
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$results = $statement->execute();
    
    	foreach ($results as $obj):
	    	if (!$is_admin):
	    		array_walk($obj, function($item, $key) use (&$obj) {
	    			if (array_search($key, $this->adminFields) !== FALSE):
	    				$obj[$key] = FALSE;
	    			endif;
	    		});
	    		$obj = array_filter($obj, function($value) {
	    			return $value !== FALSE;
	    		});
	    	else:
	    		if (empty($obj['GrossECPM'])):
	    			$obj['GrossECPM'] = 0;
	    		endif;
	    	endif;
	    	
	    	$publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getNetworkLossCorrectionRateFromPublisherAdZone($this->config, $obj['PublisherAdZoneID'], $this->network_loss_rate_list);
	    	
	    	if (empty($obj['eCPM'])):
	    		$obj['eCPM'] = 0;
	    	endif;
	    	
	    	if ($publisher_impressions_network_loss_rate > 0):
	    	
		    	$obj['Requests'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj['Requests']);
		    	$obj['Impressions'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj['Impressions']);
		    	$obj['Revenue'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj['Revenue']);
		    	if ($is_admin):
		    		$obj['GrossRevenue'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj['GrossRevenue']);
				endif;
		    endif;
		    	
	    	$obj['MDYH'] = 'DATE SPAN';
	    	$obj_list[] = $obj;
    	endforeach;

    	return $obj_list;
    }
    
    public function getPerTime($where_params = null, $is_admin = 0) {

        $obj_list = array();

        $low_range = $high_range = time();
        
        if (!empty($where_params['DateCreatedGreater'])):
       		$low_range = strtotime($where_params['DateCreatedGreater']);
        endif;
        
        if (!empty($where_params['DateCreatedLower'])):
        	$high_range = strtotime($where_params['DateCreatedLower']);
        endif;   

        $date_span = $high_range - $low_range;
        
        // if span is greater than 2 days switch to custom reporting format
        $switch_to_custom_threshold = 2 * 86400;
        
        if ($date_span > $switch_to_custom_threshold):
        	return $this->getPerTimeCustom($where_params, $is_admin);
        endif;

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('PublisherImpressionsAndSpendHourly');
        if (!empty($where_params['DateCreatedGreater'])):
            $select->where(
                    $select->where->greaterThanOrEqualTo('DateCreated', $where_params['DateCreatedGreater'])
            );
        endif;

        if (!empty($where_params['DateCreatedLower'])):
            $select->where(
                    $select->where->lessThanOrEqualTo('DateCreated', $where_params['DateCreatedLower'])
            );
        endif;

        foreach ($where_params as $name => $value):
	        if ($name != 'DateCreatedLower' && $name != 'DateCreatedGreater'):
		        $select->where(
		        		$select->where->equalTo($name, $value)
		        );
	        endif;
        endforeach;
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        foreach ($results as $obj):
            if (!$is_admin):
                array_walk($obj, function($item, $key) use (&$obj) {
                    if (array_search($key, $this->adminFields) !== FALSE) {
                        $obj[$key] = FALSE;
                    }
                });
                $obj = array_filter($obj, function($value) {
                    return $value !== FALSE;
                });
            else:
	            if (empty($obj['GrossECPM'])):
	            	$obj['GrossECPM'] = 0;
	            endif;
            endif;
            
            $publisher_impressions_network_loss_rate = \util\NetworkLossCorrection::getNetworkLossCorrectionRateFromPublisherAdZone($this->config, $obj['PublisherAdZoneID'], $this->network_loss_rate_list);

            if (empty($obj['eCPM'])):
           		$obj['eCPM'] = 0;
            endif;
            
            if ($publisher_impressions_network_loss_rate > 0):
            
	            $obj['Requests'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj['Requests']);
	            $obj['Impressions'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateInteger($publisher_impressions_network_loss_rate, $obj['Impressions']);
	            $obj['Revenue'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj['Revenue']);
	            if ($is_admin):
	           		$obj['GrossRevenue'] = \util\NetworkLossCorrection::correctAmountWithNetworkLossCorrectionRateMoney($publisher_impressions_network_loss_rate, $obj['GrossRevenue']);
	            endif;
            endif;
            
            $obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTimeHeader($is_admin = false) {

        $metadata = new Metadata($this->adapter);
        $header = $metadata->getColumnNames('PublisherImpressionsAndSpendHourly');
        return ($is_admin) ? $header : array_values(array_diff($header, $this->adminFields));
    }
}

;
