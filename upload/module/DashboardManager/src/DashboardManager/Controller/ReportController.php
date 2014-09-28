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

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		$extra_params = null;
		
        if ($this->is_admin) {
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
            $extra_params = array('PublisherInfoID' => $this->PublisherInfoID);
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
            return $this->redirect()->toUrl('report/demandindex');
        }


        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'index',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);

        $impression = \_factory\PublisherImpressionsAndSpendHourly::get_instance();

        $data = array(
            'dashboard_view' => 'report',
            'action' => 'index',
            'menu_tpl' => $menu_tpl,
            
            'impressions' => json_decode($this->getPerTime($impression, $extra_params), TRUE)['data'],
            'impressions_header' => $impression->getPerTimeHeader($this->is_admin),
            
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'is_admin' => $this->is_admin,
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        );

        $view = new ViewModel($data);
        return $view;
    }
    
    public function demandindexAction() {
    
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    
    	$extra_params = null;
    	
    	if ($this->is_admin) {
    		$this->adminFunctionsSufix = 'Admin';
    		$user_role = 1;
    	} elseif ($this->PublisherInfoID != null) {
    		return $this->redirect()->toUrl('report/');
    		$user_role = 2;
    	} elseif ($this->DemandCustomerInfoID != null) {
    		$user_role = 3;
    		$extra_params = array('DemandCustomerInfoID' => $this->DemandCustomerInfoID);
    	}
    
    
    	$view = new ViewModel();
    	$view->setTerminal(true);
    	$view->setTemplate('dashboard-manager/report/header.phtml');
    	$view->setVariables(array(
    			'action' => 'index',
    			'user_role' => $user_role
    	));
    
    	$menu_tpl = $this->getServiceLocator()
    	->get('viewrenderer')
    	->render($view);
    
    	$impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
    
    	$data = array(
    			'dashboard_view' => 'report',
    			'action' => 'index',
    			'menu_tpl' => $menu_tpl,
    
    			'impressions' => json_decode($this->getPerTime($impression /* , add where here for security */), TRUE)['data'],
    			'impressions_header' => $impression->getPerTimeHeader($this->is_admin),
    
    			'user_tld_statistic' => $impression->getUserTLDStatistic(),
    			'user_tld_statistic_header' => $impression->getUserTLDStatisticHeader(),
    			'user_id_list' => $this->user_id_list,
    			'user_identity' => $this->identity(),
    			'true_user_name' => $this->auth->getUserName(),
    			'header_title' => 'Reports',
    			'is_admin' => $this->is_admin,
    			'effective_id' => $this->auth->getEffectiveIdentityID(),
    			'impersonate_id' => $this->ImpersonateID
    	);
    
    	$view = new ViewModel($data);
    	return $view;
    }

    public function incomingBidsAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
            return $this->redirect()->toUrl('/report/');
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
            return $this->redirect()->toUrl('/report/spend');
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'incomingBids',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);

        $incoming_bid = \_factory\BuySideHourlyBidsCounter::get_instance();

        $view = new ViewModel(array(
            'action' => 'incomingBids',
            'menu_tpl' => $menu_tpl,
            'incoming_bids' => (array) json_decode($this->getIncomingBidsPerTimeAction()),
            'incoming_bids_header' => $incoming_bid->getPerTimeHeader($this->is_admin),
            'average_bids' => $incoming_bid->{'getAverage' . $this->adminFunctionsSufix}(),
            'average_bids_header' => $incoming_bid->{'getAverageHeader' . $this->adminFunctionsSufix}(),
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'is_admin' => $this->is_admin,
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        ));

        return $view;
    }

    public function outgoingBidsAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
            return $this->redirect()->toUrl('/report/');
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
            return $this->redirect()->toUrl('/report/spend');
        }


        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'outgoingBids',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);

        $outgoing_bid = \_factory\SellSidePartnerHourlyBids::get_instance();

        $view = new ViewModel(array(
            'dashboard_view' => 'report',
            'action' => 'outgoingBids',
            'menu_tpl' => $menu_tpl,
            'outgoing_bids' => (array) json_decode($this->getOutgoingBidsPerTimeAction()),
            'outgoing_bids_header' => $outgoing_bid->getPerTimeHeader($this->is_admin),
            'spend_per_webdomain' => $outgoing_bid->getPerZone(),
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'is_admin' => $this->is_admin,
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        ));
        return $view;
    }

    public function contractImpressionsAction() {
        
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
            return $this->redirect()->toUrl('incomingBids');
        }


        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'contractImpressions',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);

        $impression = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();
//        die();
        $view = new ViewModel(array(
            'dashboard_view' => 'report',
            'menu_tpl' => $menu_tpl,
            'action' => 'contractImpressions',
            'impressions' => (array) json_decode($this->getContractImpressionsPerTimeAction()),
            'spend_per_webdomain' => $impression->getPerZone(),
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'is_admin' => $this->is_admin,
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        ));
        return $view;
    }

    public function spendAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
            return $this->redirect()->toUrl('report/');
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
        }


        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'spend',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);


        $impression_spend = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();
        $data = array(
            'dashboard_view' => 'report',
            'action' => 'spend',
            'menu_tpl' => $menu_tpl,
            'impressions_spend' => (array) json_decode($this->getImpressionsCurrentSpendPerTimeAction()),
            'impressions_spend_header' => $impression_spend->getPerTimeHeader($this->is_admin),
            'user_spend_statistic' => $impression_spend->getUserImpressionsSpend($this->is_admin),
            'user_spend_statistic_header' => $impression_spend->getUserImpressionsSpendHeaders($this->is_admin),
            'is_admin' => $this->is_admin,
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        );

        $view = new ViewModel($data);
        return $view;
    }

    public function chartsAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
        }


        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'charts',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);


        $view = new ViewModel(array(
            'action' => 'charts',
            'menu_tpl' => $menu_tpl,
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'is_admin' => $this->is_admin,
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        ));
        return $view;
    }

    public function mailerAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        } elseif ($this->PublisherInfoID != null) {
            $user_role = 2;
        } elseif ($this->DemandCustomerInfoID != null) {
            $user_role = 3;
        }


        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('dashboard-manager/report/header.phtml');
        $view->setVariables(array(
            'action' => 'mailer',
            'user_role' => $user_role
        ));

        $menu_tpl = $this->getServiceLocator()
                ->get('viewrenderer')
                ->render($view);


        $ReportSubscription = \_factory\ReportSubscription::get_instance();
        $params = $this->params()->fromPost();

        if (!empty($params['action']) && $params['action'] == 'update_subscription') {
            if (!empty($params['subscription'])) {
                $status = 1;
            } else {
                $status = 0;
            }
            $subscription_record = $ReportSubscription->get_row(array('UserId' => $this->EffectiveID));
            $subscription_record['Status'] = $status;
            $subscription_record['UserID'] = $this->EffectiveID;
            $ReportSubscriptionModel = new \model\ReportSubscription();
            $ReportSubscriptionModel->initialize($subscription_record);
            $ReportSubscription->updateReportSubscription($ReportSubscriptionModel);
        } else {
            $subscription_record = $ReportSubscription->get_row(array('UserId' => $this->EffectiveID));
        }
        $view = new ViewModel(array(
            'action' => 'mailer',
            'menu_tpl' => $menu_tpl,
            'subscription' => $subscription_record,
            'user_id_list' => $this->user_id_list,
            'user_identity' => $this->identity(),
            'true_user_name' => $this->auth->getUserName(),
            'header_title' => 'Reports',
            'is_admin' => $this->is_admin,
            'effective_id' => $this->auth->getEffectiveIdentityID(),
            'impersonate_id' => $this->ImpersonateID
        ));
        return $view;
    }

    public function getUserTLDStatisticAction() {

        $impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();

        $data = array(
            'data' => $impression->getUserTLDStatistic(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getUserImpressionsSpendAction() {

        $impression_spend = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();

        $data = array(
            'data' => $impression_spend->getUserImpressionsSpend(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getAverageIncomingBidsAction() {

        $incoming_bid = \_factory\BuySideHourlyBidsCounter::get_instance();

        $data = array(
            'data' => $incoming_bid->getAverage(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getOutgoingBidsPerZoneAction() {

        $outgoing_bid = \_factory\SellSidePartnerHourlyBids::get_instance();

        $data = array(
            'data' => $outgoing_bid->getPerZone(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getImpressionsPerContractZoneAction() {

        $impressions = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();

        $data = array(
            'data' => $impressions->getPerZone(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getDemandImpressionsPerTimeAction() {
    	
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	 
    	$extra_params = null;
    	 
    	if (!$this->is_admin && $this->DemandCustomerInfoID != null):
    		$user_role = 2;
    		$extra_params = array('DemandCustomerInfoID' => $this->DemandCustomerInfoID);
    	elseif ($this->DemandCustomerInfoID == null):
    		die("bad request");
    	endif;
    	
        return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\BuySideHourlyImpressionsByTLD::get_instance() /* , add where here for security */ )
        );
    }
  
    public function getPublisherImpressionsPerTimeAction() {
    	
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	$extra_params = null;
    	
    	if (!$this->is_admin && $this->PublisherInfoID != null):
    		$user_role = 2;
    		$extra_params = array('PublisherInfoID' => $this->PublisherInfoID);
    	elseif ($this->PublisherInfoID == null):
    		die("bad request");
    	endif;
    	
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\PublisherImpressionsAndSpendHourly::get_instance(), $extra_params)
    	);
    }

    public function getIncomingBidsPerTimeAction() {

    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\BuySideHourlyBidsCounter::get_instance())
    	);
    }

    public function getOutgoingBidsPerTimeAction() {

    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\SellSidePartnerHourlyBids::get_instance())
    	);
    }

    public function getContractImpressionsPerTimeAction() {
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\ContractPublisherZoneHourlyImpressions::get_instance())
    	);
    }

    public function getImpressionsCurrentSpendPerTimeAction() {
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance())
    	);
    }

    private function getPerTime($obj, $extra_params = null) {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin) {
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
        } elseif ($this->PublisherInfoID != null) {
            
        } elseif ($this->DemandCustomerInfoID != null) {
            
        }


        $params = $this->params()->fromQuery();
        if (!empty($params['step'])) {
            $step = $params['step'];
        } else {
            $step = 1;
        }

//        $DateCreatedGreater = date('Y-m-d H:i:s', time() - 15 * $step * 60);
        $DateCreatedGreater = '2010-12-12 12:12:12';
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

        if ($extra_params !== null):
        	foreach ($extra_params as $key => $value):
        		$where_params[$key] = $value;
        	endforeach;
        endif;
        
        if (!empty($params['refresh'])) {
            $refresh = true;
        } else {
            $refresh = false;
        }

        $data = array(
            'data' => $obj->getPerTimeCached($this->config_handle, $where_params, 900, $refresh, $this->is_admin),
            'step' => $step
        );
        
        return json_encode($data);

    }

}
