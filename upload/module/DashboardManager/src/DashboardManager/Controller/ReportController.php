<?php

/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\PublisherAbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * @author Kelvin Mok
 * This is the ManagerController that is the initial display of the Manager class.
 */
class ReportController extends PublisherAbstractActionController {

    private $adminFunctionsSufix = '';
    

    public function indexAction() {
        
        $this->initialize();

        if ($this->is_admin) {
    		$this->EffectiveID;
    		$this->adminFunctionsSufix = 'Admin';
    	
    	}

        $impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
        $impression_spend = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();
        $data = array(
            'dashboard_view' => 'report',
            'action' => 'index',
            'impressions' => (array)json_decode($this->getImpressionsPerTimeAction()),
            'user_tld_statistic' => $impression->getUserTLDStatistic(),
        );
        
        $view = new ViewModel($data);
        return $view;
    }

    public function incomingBidsAction() {

        $this->initialize();

        if ($this->is_admin) {
    		$this->EffectiveID;
    		$this->adminFunctionsSufix = 'Admin';
    	}

        $incoming_bid = \_factory\BuySideHourlyBidsCounter::get_instance();

        $view = new ViewModel(array(
            'action' => 'incomingBids',
            'incoming_bids' => (array)json_decode($this->getIncomingBidsPerTimeAction()),
            'incoming_bids_header' => $incoming_bid->getPerTimeHeader($this->is_admin),
            'average_bids' => $incoming_bid->{'getAverage' . $this->adminFunctionsSufix}(),
            'average_bids_header' => $incoming_bid->{'getAverageHeader' . $this->adminFunctionsSufix}(),
        ));
        return $view;

    }

    public function outgoingBidsAction() {

        $this->initialize();

        if ($this->is_admin) {
    		$this->EffectiveID;
    		$this->adminFunctionsSufix = 'Admin';
    	}
        
        $outgoing_bid = \_factory\SellSidePartnerHourlyBids::get_instance();
        
        $view = new ViewModel(array(
            'dashboard_view' => 'report',
            'action' => 'outgoingBids',
            'outgoing_bids' => (array)json_decode($this->getOutgoingBidsPerTimeAction()),
            'outgoing_bids_header' => $outgoing_bid->getPerTimeHeader($this->is_admin),
            'spend_per_webdomain' => $outgoing_bid->getPerZone(),
        ));
        return $view;

    }
    
    public function contractImpressionsAction() {

        $impression = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();
//        die();
        $view = new ViewModel(array(
            'dashboard_view' => 'report',
            'action' => 'contractImpressions',
            'impressions' => (array)json_decode($this->getContractImpressionsPerTimeAction()),
            'spend_per_webdomain' => $impression->getPerZone(),
        ));
        return $view;

    }
    
    public function spendAction() {
        
        $this->initialize();

        if ($this->is_admin) {
    		$this->EffectiveID;
    		$this->adminFunctionsSufix = 'Admin';
    	
    	}

        $impression_spend = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();
        $data = array(
            'dashboard_view' => 'report',
            'action' => 'spend',
            'impressions_spend' => (array)json_decode($this->getImpressionsCurrentSpendPerTimeAction()),
            'impressions_spend_header' => $impression_spend->getPerTimeHeader($this->is_admin),
            'user_spend_statistic' => $impression_spend->{'getUserImpressionsSpend' . $this->adminFunctionsSufix}(),
            'user_spend_statistic_header' => $impression_spend->{'getUserImpressionsSpendHeaders' . $this->adminFunctionsSufix}(),
            'is_admin' => $this->is_admin,
        );
        
        $view = new ViewModel($data);
        return $view;
    }
    
    public function chartsAction(){
        
        $view = new ViewModel(array(
            'action' => 'charts',
        ));
        return $view;
        
    }

    public function mailerAction(){
        
        $ReportSubscription = \_factory\ReportSubscription::get_instance();
        $params = $this->params()->fromPost();
        
        if(!empty($params['action']) && $params['action'] == 'update_subscription'){
            if(!empty($params['subscription'])){
                $status = 1;
            }
            else{
                $status = 0;
            }
            $subscription_record = $ReportSubscription->get_row(array('UserId' => $this->EffectiveID));
            $subscription_record['Status'] = $status;
            $subscription_record['UserID'] = $this->EffectiveID;
            $ReportSubscriptionModel = new \model\ReportSubscription();
            $ReportSubscriptionModel->initialize($subscription_record);
            $ReportSubscription->updateReportSubscription($ReportSubscriptionModel);
        }
        else{
            $subscription_record = $ReportSubscription->get_row(array('UserId' => $this->EffectiveID));
        }
        $view = new ViewModel(array(
            'action' => 'mailer',
            'subscription' => $subscription_record,
        ));
        return $view;
        
        
    }
    

    public function getUserTLDStatisticAction(){
        
        $impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
        
        $data  = array(
            'data' => $impression->getUserTLDStatistic(),
            );
        return $this->getResponse()->setContent(json_encode($data));
        
    }
    
    public function getUserImpressionsSpendAction(){
        
        $impression_spend = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();
        
        $data  = array(
            'data' => $impression_spend->getUserImpressionsSpend(),
            );
        return $this->getResponse()->setContent(json_encode($data));
    }
    
    public function getAverageIncomingBidsAction(){
        
        $incoming_bid = \_factory\BuySideHourlyBidsCounter::get_instance();
        
        $data  = array(
            'data' => $incoming_bid->getAverage(),
            );
        return $this->getResponse()->setContent(json_encode($data));
    }
    
    public function getOutgoingBidsPerZoneAction(){
        
        $outgoing_bid = \_factory\SellSidePartnerHourlyBids::get_instance();
        
        $data  = array(
            'data' => $outgoing_bid->getPerZone(),
            );
        return $this->getResponse()->setContent(json_encode($data));
    }
    
        public function getImpressionsPerContractZoneAction(){
        
        $impressions = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();
        
        $data  = array(
            'data' => $impressions->getPerZone(),
            );
        return $this->getResponse()->setContent(json_encode($data));
    }
    
    public function getImpressionsPerTimeAction(){

        return $this->getPerTime(\_factory\BuySideHourlyImpressionsByTLD::get_instance());
    }
    
    public function getIncomingBidsPerTimeAction(){
    	
        return $this->getPerTime(\_factory\BuySideHourlyBidsCounter::get_instance());
    }
    
    public function getOutgoingBidsPerTimeAction(){
        
        return $this->getPerTime(\_factory\SellSidePartnerHourlyBids::get_instance());
    }
    
    public function getContractImpressionsPerTimeAction(){
     
        return $this->getPerTime(\_factory\ContractPublisherZoneHourlyImpressions::get_instance());
    }
    
    public function getImpressionsCurrentSpendPerTimeAction(){

        return $this->getPerTime(\_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance());
    }
    
    private function getPerTime($obj){
        
        $this->initialize();

        if ($this->is_admin) {
    		$this->EffectiveID;
    		$this->adminFunctionsSufix = 'Admin';
    	}
        
        $params = $this->params()->fromQuery();
        if (!empty($params['step'])) {
            $step = $params['step'];
        } else {
            $step = 1;
        }

        $DateCreatedGreater = date('Y-m-d H:i:s', time() - 15 * $step * 60);
        $DateCreatedLower = date('Y-m-d H:i:s', time() - 15 * ($step - 1) * 60);

        if (!empty($params['step'])) {

            switch ($params['interval']) {

                case '0':
                    $where_params = array(
                        'DateCreatedGreater' => $DateCreatedGreater,
                        'DateCreatedLower' => $DateCreatedLower,
                    );
                    break;

                case '1':
                    $where_params = array(
                        'DateCreatedGreater' => ($params['time_from'] < $params['time_to']) ? $params['time_from'] : $params['time_to'],
                        'DateCreatedLower' => ($params['time_from'] > $params['time_to']) ? $params['time_from'] : $params['time_to'],
                    );
                    break;

                default:
                    return false;
                    break;
            }
        } else {
            $where_params = array(
                'DateCreatedGreater' => $DateCreatedGreater,
                'DateCreatedLower' => $DateCreatedLower,
            );
        }

        if (!empty($params['refresh'])) {
            $refresh = true;
        } else {
            $refresh = false;
        }

        $data  = array(
            'data' => $obj->getPerTimeCached($this->config_handle, $where_params, 900, $refresh, $this->is_admin),
            'step' => $step
            );
        return $this->getResponse()->setContent(json_encode($data));
        
    }
}
