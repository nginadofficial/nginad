<?php

/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Sql\Sql;
use Zend\Db\Metadata\Metadata;

class DemandImpressionsAndSpendHourly extends \_factory\CachedTableRead {

    static protected $instance = null;
    static $visibleAdminFiealds = array();
    static $visibleUserFiealds = array();

    static $perTimeCustomInvoked = false;
    
    public static function get_instance() {

        if (self::$instance == null):
            self::$instance = new \_factory\DemandImpressionsAndSpendHourly();
        endif;
        return self::$instance;
    }

    function __construct() {

        $this->table = 'DemandImpressionsAndSpendHourly';
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());

        $this->adminFields = array_merge($this->adminFields, array(
        	'DemandCustomerInfoID',
        	'DemandCustomerName',
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
            $select->order(array('MDYH', 'InsertionOrderLineItemID'));
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
            $select->order(array('MDYH', 'InsertionOrderLineItemID'));
        }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }

    public function getPerTimeCustom($where_params = null, $is_super_admin = 0) {
    
    	self::$perTimeCustomInvoked = true;
    	
    	$obj_list = array();

    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array(
    			'MDYH' => new \Zend\Db\Sql\Expression('MAX(MDYH)'),
    			'InsertionOrderLineItemID',
    			'DemandCustomerName',
    			'DemandCustomerInfoID',
    			'BannerName',
    			'PublisherTLDs' => new \Zend\Db\Sql\Expression("CONCAT('N/','A')"),
    			'Impressions' => new \Zend\Db\Sql\Expression('SUM(Impressions)'),
    			'Cost' => new \Zend\Db\Sql\Expression('SUM(Cost)'),
    			'Exchange Fees Cost' => new \Zend\Db\Sql\Expression('SUM(GrossCost) - SUM(Cost)'),
    			'CPM' => new \Zend\Db\Sql\Expression("IFNULL(AVG(CPM), '')"),
    			'Exchange Fees CPM' => new \Zend\Db\Sql\Expression("IFNULL(AVG(GrossCPM), 0) - IFNULL(AVG(CPM), 0)"),
    			'DateCreated' => new \Zend\Db\Sql\Expression('MAX(DateCreated)')
    			
    	));
    	$select->from('DemandImpressionsAndSpendHourlyPre');
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
    	
    	$select->group('InsertionOrderLineItemID');
    	$select->order('InsertionOrderLineItemID');
    	$statement = $sql->prepareStatementForSqlObject($select);
    	$results = $statement->execute();
    
    	foreach ($results as $obj):
	    	if (!$is_super_admin):
	    		array_walk($obj, function($item, $key) use (&$obj) {
	    			if (array_search($key, $this->adminFields) !== FALSE):
	    				$obj[$key] = FALSE;
	    			endif;
	    		});
	    		$obj = array_filter($obj, function($value) {
	    			return $value !== FALSE;
	    		});
	    	else:
		    	if (empty($obj['CPM'])):
		    		$obj['CPM'] = 0;
		    	endif;
	    	endif;

	    	$obj['MDYH'] = 'DATE SPAN';
	    	$obj_list[] = $obj;
    	endforeach;
    
    	return $obj_list;
    }
    
    
    public function getPerTime($where_params = null, $is_super_admin = false, $is_domain_admin = false) {

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
        	return $this->getPerTimeCustom($where_params, $is_super_admin);
        endif;
        
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('DemandImpressionsAndSpendHourly');
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
            if (!$is_super_admin):
                array_walk($obj, function($item, $key) use (&$obj) {
                    if (array_search($key, $this->adminFields) !== FALSE) {
                        $obj[$key] = FALSE;
                    }
                });
                $obj = array_filter($obj, function($value) {
                    return $value !== FALSE;
                });
	    	else:
		    	if (empty($obj['CPM'])):
		    		$obj['CPM'] = 0;
		    	endif;
	    	endif;

	    	
	    	$obj['Exchange Fees CPM'] = floatval($obj['GrossCPM']) - floatval($obj['CPM']);
	    	$obj['Exchange Fees CPM'] = (string)$obj['Exchange Fees CPM'];
	    	unset($obj['GrossCPM']);
	    	
	    	$obj['Exchange Fees Cost'] = floatval($obj['GrossCost']) - floatval($obj['Cost']);
	    	$obj['Exchange Fees Cost'] = (string)$obj['Exchange Fees Cost'];
	    	unset($obj['GrossCost']);

            $obj['MDYH'] = $this->re_normalize_time($obj['MDYH']);

            $reOrder = array(
            		'MDYH' => $obj['MDYH'],
            		'InsertionOrderLineItemID' => $obj['InsertionOrderLineItemID'],
            		'DemandCustomerName' => null,
            		'DemandCustomerInfoID' => null,
            		'BannerName' => $obj['BannerName'],
            		'PublisherTLDs' => $obj['PublisherTLDs'],
            		'Impressions' => $obj['Impressions'],
            		'Cost' => $obj['Cost'],
            		'Exchange Fees Cost' => $obj['Exchange Fees Cost'],
            		'CPM' => $obj['CPM'],
            		'Exchange Fees CPM' => $obj['Exchange Fees CPM'],
            		'DateCreated' => null
            );
            
            if (isset($obj['DemandCustomerName'])):
            	$reOrder['DemandCustomerName'] = $obj['DemandCustomerName'];
           	else:
           		unset($reOrder['DemandCustomerName']);
            endif;
            
            if (isset($obj['DemandCustomerInfoID'])):
            	$reOrder['DemandCustomerInfoID'] = $obj['DemandCustomerInfoID'];
           	else:
           		unset($reOrder['DemandCustomerInfoID']);
            endif;
            
            if (isset($obj['DateCreated'])):
            	$reOrder['DateCreated'] = $obj['DateCreated'];
           	else:
           		unset($reOrder['DateCreated']);
            endif;
            
            $obj_list[] = $reOrder;
        endforeach;

        return $obj_list;
    }

    public function getPerTimeHeader($is_super_admin = false) {

        $metadata = new Metadata($this->adapter);
        $header = $metadata->getColumnNames('DemandImpressionsAndSpendHourly');

        foreach ($header as $key => $value):
        
        	if ($header[$key] == 'GrossCost'):
        		$header[$key] = 'Exchange Fees Cost';
        	elseif ($header[$key] == 'GrossCPM'):
        		$header[$key] = 'Exchange Fees CPM';
        	endif;
        
        endforeach;
        
        return ($is_super_admin) ? $header : array_values(array_diff($header, $this->adminFields));
    }
}
