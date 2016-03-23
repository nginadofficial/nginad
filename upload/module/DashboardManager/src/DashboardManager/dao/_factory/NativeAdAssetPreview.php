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

class NativeAdAssetPreview extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\NativeAdAssetPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'NativeAdAssetPreview';
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
        	$select->order(array('NativeAdAssetPreviewID'));

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
    		$select->order(array('NativeAdAssetPreviewID'));
    	}
    		);
    
    		foreach ($resultSet as $obj):
    			$obj_list[] = $obj;
    		endforeach;
    
    		return $obj_list;
    }
   
   public function saveNativeAdAssetPreview(\model\NativeAdAssetPreview $NativeAdAssetPreview) {
   	
	   	$data = array(
	   			'NativeAdPreviewID'							=> $NativeAdAssetPreview->NativeAdPreviewID,
	   			'AssetType'       							=> $NativeAdAssetPreview->AssetType,
	   			'AssetRequired'       						=> $NativeAdAssetPreview->AssetRequired != 1 ? 0 : 1,
	   			'TitleText'         						=> $NativeAdAssetPreview->TitleText === "" ? null : $NativeAdAssetPreview->TitleText,
	   			'ImageUrl'         							=> $NativeAdAssetPreview->ImageUrl === "" ? null : $NativeAdAssetPreview->ImageUrl,
	   			'ImageWidth'         						=> $NativeAdAssetPreview->ImageWidth === "" ? null : $NativeAdAssetPreview->ImageWidth,
	   			'ImageHeight'     							=> $NativeAdAssetPreview->ImageHeight === "" ? null : $NativeAdAssetPreview->ImageHeight,		
	   			'VideoVastTag'     							=> $NativeAdAssetPreview->VideoVastTag === "" ? null : $NativeAdAssetPreview->VideoVastTag,
	   			'VideoDuration'     						=> $NativeAdAssetPreview->VideoDuration === "" ? null : $NativeAdAssetPreview->VideoDuration,
	   			'VideoMimesCommaSeparated'     				=> $NativeAdAssetPreview->VideoMimesCommaSeparated === "" ? null : $NativeAdAssetPreview->VideoMimesCommaSeparated,
	   			'VideoProtocolsCommaSeparated'  			=> $NativeAdAssetPreview->VideoProtocolsCommaSeparated === "" ? null : $NativeAdAssetPreview->VideoProtocolsCommaSeparated,
	   			'DataType'       							=> $NativeAdAssetPreview->DataType === "" ? null : $NativeAdAssetPreview->DataType,
	   			'DataLabel'       							=> $NativeAdAssetPreview->DataLabel === "" ? null : $NativeAdAssetPreview->DataLabel,
	   			'DataValue'       							=> $NativeAdAssetPreview->DataValue === "" ? null : $NativeAdAssetPreview->DataValue,
	   			'LinkUrl'       							=> $NativeAdAssetPreview->LinkUrl === "" ? null : $NativeAdAssetPreview->LinkUrl,
	   			'LinkClickTrackerUrlsCommaSeparated'		=> $NativeAdAssetPreview->LinkClickTrackerUrlsCommaSeparated === "" ? null : $NativeAdAssetPreview->LinkClickTrackerUrlsCommaSeparated,
	   			'LinkFallback'       						=> $NativeAdAssetPreview->LinkFallback === "" ? null : $NativeAdAssetPreview->LinkFallback,
	   			'DateCreated'         						=> $NativeAdAssetPreview->DateCreated
	   	);

		$this->insert($data);
		return $this->getLastInsertValue();
   }
   
   /**
    * Delete the all children of parent of the native asset
    * 
    * @param int $NativeAdAssetPreviewID The integer ID of the Parent object who's children will be deleted
    * @throws \InvalidArgumentException is thrown when an invalid integer is provided.
    * @return boolean|int Returns the rows affected, or FALSE if failure.
    */
   public function delete_assets($NativeAdAssetPreviewID)
   {
       $result = $this->delete(array("NativeAdAssetPreviewID" => intval($NativeAdAssetPreviewID)));
   }
   
};
?>