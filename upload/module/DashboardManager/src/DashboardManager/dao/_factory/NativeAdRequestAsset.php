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

class NativeAdRequestAsset extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\NativeAdAsset();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'NativeAdRequestAsset';
            $this->featureSet = new Feature\FeatureSet();
            $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
            $this->initialize();
    }

    /**
     * Query database and return a row of results.
     * 
     * @param string $params
     * @return Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>|NULL
     */
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
        	$select->order(array('NativeAdRequestAssetID'));

        }
        	);

    	    foreach ($resultSet as $obj):
    	         return $obj;
    	    endforeach;

        	return null;
    }

    /**
     * Query database and return results.
     *
     * @param string $params
     * @return multitype:Ambigous <\Zend\Db\ResultSet\ResultSet, NULL, \Zend\Db\ResultSet\ResultSetInterface>
     */
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
    		$select->order(array('NativeAdRequestAssetID'));
    	}
    		);
    
    		foreach ($resultSet as $obj):
    			$obj_list[] = $obj;
    		endforeach;
    
    		return $obj_list;
    }
   
   public function saveNativeAdRequestAsset(\model\NativeAdRequestAsset $NativeAdRequestAsset) {
   	
	   	$data = array(
	   			'NativeAdRequestID'						=> $NativeAdRequestAsset->NativeAdRequestID,
	   			'AssetType'       						=> $NativeAdRequestAsset->AssetType,
	   			'AssetRequired'       					=> $NativeAdRequestAsset->AssetRequired != 1 ? 0 : 1,
	   			'TitleText'         					=> $NativeAdRequestAsset->TitleText === "" ? null : $NativeAdRequestAsset->TitleText,
	   			'ImageUrl'         						=> $NativeAdRequestAsset->ImageUrl === "" ? null : $NativeAdRequestAsset->ImageUrl,
	   			'ImageWidth'         					=> $NativeAdRequestAsset->ImageWidth === "" ? null : $NativeAdRequestAsset->ImageWidth,
	   			'ImageHeight'     						=> $NativeAdRequestAsset->ImageHeight === "" ? null : $NativeAdRequestAsset->ImageHeight,		
	   			'VideoVastTag'     						=> $NativeAdRequestAsset->VideoVastTag === "" ? null : $NativeAdRequestAsset->VideoVastTag,
	   			'VideoDuration'     					=> $NativeAdRequestAsset->VideoDuration === "" ? null : $NativeAdRequestAsset->VideoDuration,
	   			'VideoMimesCommaSeparated'     			=> $NativeAdRequestAsset->VideoMimesCommaSeparated === "" ? null : $NativeAdRequestAsset->VideoMimesCommaSeparated,
	   			'VideoProtocolsCommaSeparated'  		=> $NativeAdRequestAsset->VideoProtocolsCommaSeparated === "" ? null : $NativeAdRequestAsset->VideoProtocolsCommaSeparated,
	   			'DataType'       						=> $NativeAdRequestAsset->DataType === "" ? null : $NativeAdRequestAsset->DataType,
	   			'DataLabel'       						=> $NativeAdRequestAsset->DataLabel === "" ? null : $NativeAdRequestAsset->DataLabel,
	   			'DataValue'       						=> $NativeAdRequestAsset->DataValue === "" ? null : $NativeAdRequestAsset->DataValue,
	   			'LinkUrl'       						=> $NativeAdRequestAsset->LinkUrl === "" ? null : $NativeAdRequestAsset->LinkUrl,
	   			'LinkClickTrackerUrlsCommaSeparated'	=> $NativeAdRequestAsset->LinkClickTrackerUrlsCommaSeparated === "" ? null : $NativeAdRequestAsset->LinkClickTrackerUrlsCommaSeparated,
	   			'LinkFallback'       					=> $NativeAdRequestAsset->LinkFallback === "" ? null : $NativeAdRequestAsset->LinkFallback,
	   			'DateCreated'         					=> $NativeAdRequestAsset->DateCreated
	   	);

		$this->insert($data);
		return $this->getLastInsertValue();
   }
   
   /**
    * Delete the all children of parent of the native asset
    * 
    * @param int $NativeAdRequestID The integer ID of the Parent object who's children will be deleted
    * @throws \InvalidArgumentException is thrown when an invalid integer is provided.
    * @return boolean|int Returns the rows affected, or FALSE if failure.
    */
   public function delete_assets($NativeAdRequestID)
   {
       $result = $this->delete(array("NativeAdRequestID" => intval($NativeAdRequestID)));
   }
   
};
