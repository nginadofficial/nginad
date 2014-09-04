<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace _factory;

use Zend\Db\Sql\Select;
//use Zend\Db\TableGateway\Feature;

class ViewsGetter extends Select
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\ViewsGetter();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'BuySideHourlyImpressionsCounterCurrentSpend';
//            $this->featureSet = new Feature\FeatureSet();
//            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
//            $this->initialize();
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
        	$select->order('BuySideHourlyImpressionsCounterCurrentSpendID');

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
        		$select->order('BuySideHourlyImpressionsCounterCurrentSpendID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function getUserImpressionsSpendAdmin(){

        $obj_list = array();

        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) {
                
                $select->columns(array(
                    'BuySidePartnerID',
                    'TotalSpendGross' => new \Zend\Db\Sql\Expression('ROUND(SUM(' . $this->table  . '.CurrentSpendGross), 7)'),
                    'TotalSpendNet' => new \Zend\Db\Sql\Expression('ROUND(SUM(' . $this->table  . '.CurrentSpendNet), 7)'),
                    ));
                
                $select->join(
                     'AdCampaignBanner',
                     $this->table . '.AdCampaignBannerID = AdCampaignBanner.AdCampaignBannerID',
                     array()
                );

                $select->join(
                     'AdCampaign',
                     'AdCampaignBanner.AdCampaignID = AdCampaign.AdCampaignID',
                     array('Name')
                );

                $select->join(
                     'auth_Users',
                     'auth_Users.user_id = AdCampaignBanner.UserID',
                     array('user_login')
                );

//                $select->group('AdCampaignBanner.UserID');
                $select->group('BuySidePartnerID');
                $select->group('BuySideHourlyImpressionsCounterCurrentSpend.AdCampaignBannerID');
                $select->order('user_login');

            }
        );

        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
        
    }
    public function getUserImpressionsSpendAdmin2(){
        
        $tmp = $this->table;
        $this->table = 'userImpressionsSpendAdmin';
        
        $obj_list = array();

        $resultSet = $this->select(function (\Zend\Db\Sql\Select $select) {
                $select->from($this->table);
            }
        );
        
        $this->table = $tmp;
        foreach ($resultSet as $obj):
            $obj_list[] = $obj;
        endforeach;

        return $obj_list;
    }


    public function insertBuySideHourlyImpressionsCounterCurrentSpend(\model\BuySideHourlyImpressionsCounterCurrentSpend $BuySideHourlyImpressionsCounterCurrentSpend) {

    	$data = array(
    			'BuySidePartnerID'        	=> $BuySideHourlyImpressionsCounterCurrentSpend->BuySidePartnerID,
    			'AdCampaignBannerID'        => $BuySideHourlyImpressionsCounterCurrentSpend->AdCampaignBannerID,
    			'MDYH'						=> $BuySideHourlyImpressionsCounterCurrentSpend->MDYH,
    			'ImpressionsCounter'   		=> $BuySideHourlyImpressionsCounterCurrentSpend->ImpressionsCounter,
    			'CurrentSpendGross'        	=> $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendGross,
    			'CurrentSpendNet'         	=> $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendNet,
    			'DateCreated'   	   		=> $BuySideHourlyImpressionsCounterCurrentSpend->DateCreated
    	);
    	
    	$this->insert($data);
    }

    public function updateBuySideHourlyImpressionsCounterCurrentSpend(\model\BuySideHourlyImpressionsCounterCurrentSpend $BuySideHourlyImpressionsCounterCurrentSpend) {
    	$data = array(
    			'ImpressionsCounter'   		=> $BuySideHourlyImpressionsCounterCurrentSpend->ImpressionsCounter,
    			'CurrentSpendGross'        	=> $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendGross,
    			'CurrentSpendNet'         	=> $BuySideHourlyImpressionsCounterCurrentSpend->CurrentSpendNet
    	);
    	$buyside_hourly_impressions_counter_current_spend_id = (int)$BuySideHourlyImpressionsCounterCurrentSpend->BuySideHourlyImpressionsCounterCurrentSpendID;
    	$this->update($data, array('BuySideHourlyImpressionsCounterCurrentSpendID' => $buyside_hourly_impressions_counter_current_spend_id));
    }

};
