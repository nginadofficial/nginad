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

class NativeAdAsset extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\NativeAdAsset();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'NativeAdAsset';
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
        	$select->order(array('NativeAdAssetID'));

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
    		$select->order(array('NativeAdAssetID'));
    	}
    		);
    
    		foreach ($resultSet as $obj):
    			$obj_list[] = $obj;
    		endforeach;
    
    		return $obj_list;
    }
   
   public function saveNativeAdAsset(\model\NativeAdAsset $NativeAdAsset) {
   	
	   	$data = array(
	   			'NativeAdID'							=> $NativeAdAsset->NativeAdID,
	   			'AssetType'       						=> $NativeAdAsset->AssetType,
	   			'AssetRequired'       					=> $NativeAdAsset->AssetRequired != 1 ? 0 : 1,
	   			'TitleText'         					=> $NativeAdAsset->TitleText === "" ? null : $NativeAdAsset->TitleText,
	   			'ImageUrl'         						=> $NativeAdAsset->ImageUrl === "" ? null : $NativeAdAsset->ImageUrl,
	   			'ImageWidth'         					=> $NativeAdAsset->ImageWidth === "" ? null : $NativeAdAsset->ImageWidth,
	   			'ImageHeight'     						=> $NativeAdAsset->ImageHeight === "" ? null : $NativeAdAsset->ImageHeight,		
	   			'VideoVastTag'     						=> $NativeAdAsset->VideoVastTag === "" ? null : $NativeAdAsset->VideoVastTag,
	   			'VideoDuration'     					=> $NativeAdAsset->VideoDuration === "" ? null : $NativeAdAsset->VideoDuration,
	   			'VideoMimesCommaSeparated'     			=> $NativeAdAsset->VideoMimesCommaSeparated === "" ? null : $NativeAdAsset->VideoMimesCommaSeparated,
	   			'VideoProtocolsCommaSeparated'  		=> $NativeAdAsset->VideoProtocolsCommaSeparated === "" ? null : $NativeAdAsset->VideoProtocolsCommaSeparated,
	   			'DataType'       						=> $NativeAdAsset->DataType === "" ? null : $NativeAdAsset->DataType,
	   			'DataLabel'       						=> $NativeAdAsset->DataLabel === "" ? null : $NativeAdAsset->DataLabel,
	   			'DataValue'       						=> $NativeAdAsset->DataValue === "" ? null : $NativeAdAsset->DataValue,
	   			'LinkUrl'       						=> $NativeAdAsset->LinkUrl === "" ? null : $NativeAdAsset->LinkUrl,
	   			'LinkClickTrackerUrlsCommaSeparated'	=> $NativeAdAsset->LinkClickTrackerUrlsCommaSeparated === "" ? null : $NativeAdAsset->LinkClickTrackerUrlsCommaSeparated,
	   			'LinkFallback'       					=> $NativeAdAsset->LinkFallback === "" ? null : $NativeAdAsset->LinkFallback,
	   			'DateCreated'         					=> $NativeAdAsset->DateCreated
	   	);

		$this->insert($data);
		return $this->getLastInsertValue();
   }
   
   /**
    * Delete the all children of parent of the native asset
    * 
    * @param int $InsertionOrderLineItemNativeID The integer ID of the Parent object who's children will be deleted
    * @throws \InvalidArgumentException is thrown when an invalid integer is provided.
    * @return boolean|int Returns the rows affected, or FALSE if failure.
    */
   public function delete_assets($NativeAdID)
   {
       $result = $this->delete(array("NativeAdID" => intval($NativeAdID)));
   }
   
};
