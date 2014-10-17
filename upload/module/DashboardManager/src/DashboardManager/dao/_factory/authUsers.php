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

class authUsers extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\authUsers();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'auth_Users';
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
        	$select->order('user_id');

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
        	$select->order('user_id');

        	}
    	);

    	foreach ($resultSet as $obj):
    	    $obj_list[] = $obj;
    	endforeach;

    	return $obj_list;
    }
    
    
   public function get_row_object($params = null)
   {
       $rawData = $this->get_row($params);
       $DataObj = new \model\authUsers();
       if ($rawData !== null):
       
           foreach (get_object_vars($DataObj) AS $key => $value):
           
               $DataObj->$key =$rawData[$key];
           endforeach;
       endif;

       return $DataObj;
   }
    
    public function saveUser(\model\authUsers $authUsers) {
    	
    	$data['user_id'] 			= $authUsers->user_id;
    	$data['user_description'] 	= $authUsers->user_description;
    	$data['user_fullname'] 		= $authUsers->user_fullname;
    	$data['user_login']    	  	= $authUsers->user_login;
	    $data['user_email']       	= $authUsers->user_email;
	    $data['user_password']    	= $authUsers->user_password;
	    if(isset($authUsers->PublisherInfoID)) {
			$data['PublisherInfoID']  = $authUsers->PublisherInfoID;
		}
		if(isset($authUsers->DemandCustomerInfoID)) {
			$data['DemandCustomerInfoID']  	= $authUsers->DemandCustomerInfoID;
		}
		if(isset($authUsers->user_agreement_acceptance_date)) {
			$data['user_agreement_acceptance_date']  = $authUsers->user_agreement_acceptance_date;
		}
		
		$data['user_agreement_accepted']  	= $authUsers->user_agreement_accepted;
	    $data['user_role']        			= $authUsers->user_role;
	    $data['user_verified']    			= $authUsers->user_verified;
	    $data['user_enabled']     			= $authUsers->user_enabled;
	    
    	$user_id = (int)$authUsers->user_id;
    	if ($user_id === 0): 
    		$data['create_date']  = $authUsers->create_date;
    		$this->insert($data);
    		return $this->getLastInsertValue();

    	else: 
    		$data['update_date']  =  $authUsers->update_date;
			return $this->update($data, array('user_id' => $user_id));    		
    	endif;
 
    }
    
    public function delete_user($user_id) {
    	return $this->delete(array('user_id' => $user_id));
    }

};
