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

class NativeAdPreview extends \_factory\CachedTableRead
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\NativeAdPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'NativeAdPreview';
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
        	$select->order(array('NativeAdPreviewID'));

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
    		$select->order(array('NativeAdPreviewID'));
    	}
    		);
    
    		foreach ($resultSet as $obj):
    			$obj_list[] = $obj;
    		endforeach;
    
    		return $obj_list;
    }

   public function saveNativeAdPreview(\model\NativeAdPreview $NativeAdPreview) {
   	
   		$this->delete_zone($NativeAdPreview->InsertionOrderLineItemPreviewID);
   		
	   	$data = array(
	   			'InsertionOrderLineItemPreviewID'   	=> $NativeAdPreview->InsertionOrderLineItemPreviewID,
	   			'BidUnit'         						=> $NativeAdPreview->BidUnit,
	   			'LinkUrl'         						=> $NativeAdPreview->LinkUrl,
	   			'LinkClickTrackerUrlsCommaSeparated'	=> $NativeAdPreview->LinkClickTrackerUrlsCommaSeparated,
	   			'LinkFallback'         					=> $NativeAdPreview->LinkFallback,
	   			'TrackerUrlsCommaSeparated'    			=> $NativeAdPreview->TrackerUrlsCommaSeparated === "" ? null : $NativeAdPreview->TrackerUrlsCommaSeparated,
	   			'JsLinkTracker'         				=> $NativeAdPreview->JsLinkTracker === "" ? null : $NativeAdPreview->JsLinkTracker,
	   			'AllowedLayoutsCommaSeparated'    		=> $NativeAdPreview->AllowedLayoutsCommaSeparated === "" ? null : $NativeAdPreview->AllowedLayoutsCommaSeparated,
	   			'AllowedAdUnitsCommaSeparated'    		=> $NativeAdPreview->AllowedAdUnitsCommaSeparated === "" ? null : $NativeAdPreview->AllowedAdUnitsCommaSeparated,
	   			'MaxPlacements'         				=> $NativeAdPreview->MaxPlacements === "" ? null : $NativeAdPreview->MaxPlacements,
	   			'MaxSequence'         					=> $NativeAdPreview->MaxSequence === "" ? null : $NativeAdPreview->MaxSequence,
	   			'DateCreated'         					=> $NativeAdPreview->DateCreated
	   	);

		$this->insert($data);
		return $this->getLastInsertValue();
   }
   
   /**
    * Delete the Ad specified.
    * 
    * @param int $InsertionOrderLineItemPreviewID The integer ID of the Ad to delete.
    * @throws \InvalidArgumentException is thrown when an invalid integer is provided.
    * @return boolean|int Returns the rows affected, or FALSE if failure.
    */
   public function delete_zone($InsertionOrderLineItemPreviewID)
   {
   		$params = array();
   		$params["InsertionOrderLineItemPreviewID"] = $InsertionOrderLineItemPreviewID;
   		$NativeAdPreview = $this->get_row($params);
   		
   		$NativeAdAssetPreviewFactory = \_factory\NativeAdAssetPreview::get_instance();
   		
   		if ($NativeAdPreview != null):
   		
	   		$NativeAdAssetPreviewFactory->delete_assets($NativeAdPreview->NativeAdPreviewID);
	   		
	       	$result = $this->delete(array("InsertionOrderLineItemPreviewID" => intval($InsertionOrderLineItemPreviewID)));
	   		
   		endif;
   }
   
};
?>