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

class AdCampaignBannerPreview extends AbstractTableGateway
{

	static protected $instance = null;

	public static function get_instance() {

		if (self::$instance == null):
			self::$instance = new \_factory\AdCampaignBannerPreview();
		endif;
		return self::$instance;
	}


    function __construct() {

            $this->table = 'AdCampaignBannerPreview';
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
        	$select->order('AdCampaignBannerPreviewID');

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
        		$select->order('AdCampaignBannerPreviewID');

        	}
    	);

    	    foreach ($resultSet as $obj):
    	        $obj_list[] = $obj;
    	    endforeach;

    		return $obj_list;
    }

    public function saveAdCampaignBannerPreview(\model\AdCampaignBannerPreview $BannerPreview) {
    	$data = array(
    			'AdCampaignPreviewID'         	=> $BannerPreview->AdCampaignPreviewID,
    			'Name'                 			=> $BannerPreview->Name,
    			'ImpressionType'       			=> $BannerPreview->ImpressionType,
    			// convert to MySQL DateTime
    			'StartDate'            			=> $BannerPreview->StartDate,
    			'EndDate'              			=> $BannerPreview->EndDate,
    			'IsMobile'             			=> $BannerPreview->IsMobile,
    			'IABSize'              			=> $BannerPreview->IABSize,
    			'Height'               			=> $BannerPreview->Height,
    			'Width'                			=> $BannerPreview->Width,
    			'Weight'                		=> $BannerPreview->Weight,
    			'BidAmount'            			=> $BannerPreview->BidAmount,
    			'AdTag'                			=> $BannerPreview->AdTag,
    			'DeliveryType'       			=> $BannerPreview->DeliveryType,
    	        'LandingPageTLD'       			=> $BannerPreview->LandingPageTLD,
    	        'Active'               			=> $BannerPreview->Active,
    			'ChangeWentLive'          		=> $BannerPreview->ChangeWentLive,
    			'WentLiveDate'          		=> $BannerPreview->WentLiveDate
    	);

    	if (intval($BannerPreview->AdCampaignTypeID)):
    		$data['AdCampaignTypeID'] = $BannerPreview->AdCampaignTypeID;
    	endif;
    	
    	if (isset($BannerPreview->AdCampaignBannerID) && !empty($BannerPreview->AdCampaignBannerID)):
    		$data['AdCampaignBannerID'] = $BannerPreview->AdCampaignBannerID;
    	endif;

    	$banner_preview_id = (int)$BannerPreview->AdCampaignBannerPreviewID;
    	if ($banner_preview_id === 0): 
    		$data['UserID'] 			= $BannerPreview->UserID;
    		$data['ImpressionsCounter'] = $BannerPreview->ImpressionsCounter;
    		$data['BidsCounter']        = $BannerPreview->BidsCounter;
    		$data['CurrentSpend']       = $BannerPreview->CurrentSpend;
    		$data['DateCreated']        = $BannerPreview->DateCreated;
    		$this->insert($data);
    		return $this->getLastInsertValue();
    	else: 
    		$this->update($data, array('AdCampaignBannerPreviewID' => $banner_preview_id));
    		return null;
    	endif;
    }

    public function deleteAdCampaignBannerPreview($banner_preview_id) {
    	$this->delete(array('AdCampaignBannerPreviewID' => $banner_preview_id));
    }

    public function deActivateAdCampaignBannerPreview($banner_preview_id) {

        $params = array();
        $params["AdCampaignBannerPreviewID"] = $banner_preview_id;
        $AdCampaignBannerPreview = $this->get_row($params);

        $AdCampaignBannerPreview->Active = 0;
        // get array of data
        $data = $AdCampaignBannerPreview->getArrayCopy();

        $this->update($data, array('AdCampaignBannerPreviewID' => $banner_preview_id));
    }

};
