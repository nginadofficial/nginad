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
 * @author Kelvin Mok - Kelvin did not author this controller, 
 * I have no idea why this is here. I am guessing somebody copy/pasted.
 * This is the ReportController that is the initial display of the ReportController class.
 */
class ReportController extends PublisherAbstractActionController {

    private $adminFunctionsSufix = '';

    public function indexAction() {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
		
		$extra_params = array();
		
        if ($this->is_admin):
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
            // admin is logged in as a user, get the stats for just that user
            if ($this->ImpersonateID != 0 && !empty($this->PublisherInfoID)):
            	$extra_params = array('PublisherInfoID' => $this->PublisherInfoID);
            endif;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
            $extra_params = array('PublisherInfoID' => $this->PublisherInfoID);
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
            return $this->redirect()->toUrl('report/demandindex');
        endif;


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

        $impression = \_factory\PublisherImpressionsAndSpendHourly::get_instance($this->config_handle);

        $stats	= json_decode($this->getPerTime($impression, $extra_params), TRUE);
        
        $data = array(
            'dashboard_view' => 'report',
            'action' => 'index',
            'menu_tpl' => $menu_tpl,
            
            'impressions' => $stats['data'],
            'impressions_header' => $impression->getPerTimeHeader($this->is_admin),
        	'totals' => $stats['totals'],
        		
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
    
    	$extra_params = $extra_params_user = array();
    	
    	if ($this->is_admin):
    		$this->adminFunctionsSufix = 'Admin';
    		$user_role = 1;
    		// admin is logged in as a user, get the stats for just that user
    		if ($this->ImpersonateID != 0 && !empty($this->DemandCustomerInfoID)):
	    		$extra_params = array('DemandCustomerInfoID' => $this->DemandCustomerInfoID);
    		endif;
    	elseif ($this->PublisherInfoID != null):
    		return $this->redirect()->toUrl('report/');
    		$user_role = 2;
    	elseif ($this->DemandCustomerInfoID != null):
    		$user_role = 3;
    		$extra_params = array('DemandCustomerInfoID' => $this->DemandCustomerInfoID);
    	endif;
    
    
    	$view = new ViewModel();
    	$view->setTerminal(true);
    	$view->setTemplate('dashboard-manager/report/header.phtml');
    	$view->setVariables(array(
    			'action' => 'demandindex',
    			'user_role' => $user_role
    	));
    
    	$menu_tpl = $this->getServiceLocator()
    	->get('viewrenderer')
    	->render($view);
    
    	$impression = \_factory\DemandImpressionsAndSpendHourly::get_instance();
    
    	$stats	= json_decode($this->getPerTime($impression, $extra_params), TRUE);
    	
    	$data = array(
    			'dashboard_view' => 'report',
    			'action' => 'demandindex',
    			'menu_tpl' => $menu_tpl,
    			'impressions' => $stats['data'],
    			'impressions_header' => $impression->getPerTimeHeader($this->is_admin),
    			'totals' => $stats['totals'],
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
    
    public function usertotalsAction() {
    
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    
    	$extra_params = $extra_params_user = array();
    	 
    	if (!$this->is_admin):
    		return $this->redirect()->toUrl('report/');
    	else:	
	    	$this->adminFunctionsSufix = 'Admin';
	    	$user_role = 1;
    	endif;
    
    
    	$view = new ViewModel();
    	$view->setTerminal(true);
    	$view->setTemplate('dashboard-manager/report/header.phtml');
    	$view->setVariables(array(
    			'action' => 'usertotals',
    			'user_role' => $user_role
    	));
    
    	$menu_tpl = $this->getServiceLocator()
    	->get('viewrenderer')
    	->render($view);
    
    	$user_tld_impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
    	
    	$user_tld_statistic 		= $user_tld_impression->getUserTLDStatistic($extra_params_user);
    	$user_tld_statistic_header 	= $user_tld_impression->getUserTLDStatisticHeader();
    	
    	$totals = $this->createTotals($user_tld_statistic);
    	
    	$totals["PublisherTLD"] = "Totals:";

    	$data = array(
    			'dashboard_view' => 'report',
    			'action' => 'demandindex',
    			'menu_tpl' => $menu_tpl,
    			'user_tld_statistic' => $user_tld_statistic,
    			'user_tld_statistic_header' => $user_tld_statistic_header,
    			'totals' => $totals,
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

        if ($this->is_admin):
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
            return $this->redirect()->toUrl('/report/');
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
            return $this->redirect()->toUrl('/report/spend');
        endif;

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
        $extra_params = array();
        $stats	= json_decode($this->getPerTime($incoming_bid, $extra_params), TRUE);

        $view = new ViewModel(array(
            'action' => 'incomingBids',
            'menu_tpl' => $menu_tpl,
            'incoming_bids' => $stats['data'],
        	'totals' => $stats['totals'],
            'incoming_bids_header' => $incoming_bid->getPerTimeHeader($this->is_admin),
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

        if ($this->is_admin):
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
            return $this->redirect()->toUrl('/report/');
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
            return $this->redirect()->toUrl('/report/spend');
        endif;


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
        $extra_params = array();
        $stats	= json_decode($this->getPerTime($outgoing_bid, $extra_params), TRUE);
        
        $view = new ViewModel(array(
            'dashboard_view' => 'report',
            'action' => 'outgoingBids',
            'menu_tpl' => $menu_tpl,
            'outgoing_bids' => $stats['data'],
        	'totals' => $stats['totals'],
            'outgoing_bids_header' => $outgoing_bid->getPerTimeHeader($this->is_admin),
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
        if ($this->is_admin):
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
            return $this->redirect()->toUrl('incomingBids');
        endif;


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
        
        $stats	= json_decode($this->getPerTime($impression), TRUE);
        
        $view = new ViewModel(array(
            'dashboard_view' => 'report',
            'menu_tpl' => $menu_tpl,
            'action' => 'contractImpressions',
        	'impressions_header' => $impression->getPerTimeHeader($this->is_admin),
            'impressions' => $stats['data'],
        	'totals' 	=> $stats['totals'],
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

        if ($this->is_admin):
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
            return $this->redirect()->toUrl('report/');
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
        endif;


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

    	die("bad request"); 	
    	
    	/* 
    	 * Mike did not implement this before the release date
    	 */
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin):
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
        endif;


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

    	die("under construction");
    	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin):
            $this->EffectiveID;
            $this->adminFunctionsSufix = 'Admin';
            $user_role = 1;
        elseif ($this->PublisherInfoID != null):
            $user_role = 2;
        elseif ($this->DemandCustomerInfoID != null):
            $user_role = 3;
       	endif;


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

        if (!empty($params['action']) && $params['action'] == 'update_subscription'):
            if (!empty($params['subscription'])):
                $status = 1;
            else:
                $status = 0;
            endif;
            $subscription_record = $ReportSubscription->get_row(array('UserId' => $this->EffectiveID));
            $subscription_record['Status'] = $status;
            $subscription_record['UserID'] = $this->EffectiveID;
            $ReportSubscriptionModel = new \model\ReportSubscription();
            $ReportSubscriptionModel->initialize($subscription_record);
            $ReportSubscription->updateReportSubscription($ReportSubscriptionModel);
        else:
            $subscription_record = $ReportSubscription->get_row(array('UserId' => $this->EffectiveID));
        endif;
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

    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	$extra_params_user = array();
    	
    	if (!$this->is_admin):
    		$user_role = 1;
    		die("bad request");
    	endif;
    	
        $impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();

        $data = array(
            'data' => $impression->getUserTLDStatistic($extra_params_user),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }
    
    public function getUserTLDStatisticExcelAction() {
    
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	 
    	$extra_params_user = array();
    	 
    	if (!$this->is_admin):
    		$user_role = 1;
    		die("bad request");
    	endif;
    	 
    	$user_tld_impression = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
    	 
    	$user_tld_statistic 		= $user_tld_impression->getUserTLDStatistic($extra_params_user);
    	$user_tld_statistic_header 	= $user_tld_impression->getUserTLDStatisticHeader();
    	
    	$totals = $this->createTotals($user_tld_statistic);

    	$dates = $this->getDatesForExcelReport();
    	
    	$title = "User Totals";
    	
    	\util\ReportHelper::download_excel_file($user_tld_statistic, array_values($user_tld_statistic_header), $totals, $title, $dates);

    }
    
    public function getUserImpressionsSpendAction() {

    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
        $impression_spend = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();

        $data = array(
            'data' => $impression_spend->getUserImpressionsSpend(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getAverageIncomingBidsAction() {

    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
        $incoming_bid = \_factory\BuySideHourlyBidsCounter::get_instance();

        $data = array(
            'data' => $incoming_bid->getAverage(),
        );
        return $this->getResponse()->setContent(json_encode($data));
    }

    public function getDemandImpressionsPerTimeAction() {
    	
    	$initialized = $this->initialize(); 
    	if ($initialized !== true) return $initialized;
    	 
    	$extra_params = array();
    	 
    	if ($this->DemandCustomerInfoID != null):
    		$user_role = 2;
    		$extra_params = array('DemandCustomerInfoID' => $this->DemandCustomerInfoID);
    	elseif (!$this->is_admin):
    		die("bad request");
    	endif;
    	
        return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\DemandImpressionsAndSpendHourly::get_instance(), $extra_params)
        );
    }
    
    public function getDemandImpressionsPerTimeExcelAction() {
    
    	$initialized = $this->initialize(); 
    	if ($initialized !== true) return $initialized;
    	 
    	$extra_params = array();
    	 
    	if ($this->DemandCustomerInfoID != null):
    		$user_role = 2;
    		$extra_params = array('DemandCustomerInfoID' => $this->DemandCustomerInfoID);
    	elseif (!$this->is_admin):
    		die("bad request");
    	endif;
    
    	$impression = \_factory\DemandImpressionsAndSpendHourly::get_instance();
    
    	$stats	= json_decode($this->getPerTime($impression, $extra_params), TRUE);
    	 
    	$impression_headers = $impression->getPerTimeHeader($this->is_admin);
    
    	$dates = $this->getDatesForExcelReport();
    	 
    	$title = "Demand Impressions";
    
    	\util\ReportHelper::download_excel_file($stats['data'], $impression_headers, $stats['totals'], $title, $dates);
    }
  
    public function getPublisherImpressionsPerTimeAction() {
    	
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	$extra_params = array();
    	
    	if ($this->PublisherInfoID != null):
    		$user_role = 2;
    		$extra_params = array('PublisherInfoID' => $this->PublisherInfoID);
    	elseif (!$this->is_admin):
    		die("bad request");
    	endif;
    	
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\PublisherImpressionsAndSpendHourly::get_instance($this->config_handle), $extra_params)
    	);
    }
    
    public function getPublisherImpressionsPerTimeExcelAction() {
    	 
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	 
    	$extra_params = array();
    	 
    	if ($this->PublisherInfoID != null):
    		$user_role = 2;
    		$extra_params = array('PublisherInfoID' => $this->PublisherInfoID);
    	elseif (!$this->is_admin):
    		die("bad request");
    	endif;

    	$impression = \_factory\PublisherImpressionsAndSpendHourly::get_instance($this->config_handle);
    	 
    	$stats	= json_decode($this->getPerTime($impression, $extra_params), TRUE);
    	
    	$impression_headers = $impression->getPerTimeHeader($this->is_admin);
    	 
    	$dates = $this->getDatesForExcelReport();
    	
    	$title = "Publisher Impressions";
    
    	\util\ReportHelper::download_excel_file($stats['data'], $impression_headers, $stats['totals'], $title, $dates);
    }

    public function getIncomingBidsPerTimeAction() {

    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\BuySideHourlyBidsCounter::get_instance())
    	);
    }

    public function getIncomingBidsPerTimeExcelAction() {
    
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	 
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
    	$incoming_bid = \_factory\BuySideHourlyBidsCounter::get_instance();
    	$extra_params = array();
    	$stats	= json_decode($this->getPerTime($incoming_bid, $extra_params), TRUE);
    	
    	$incoming_bid_headers = $incoming_bid->getPerTimeHeader($this->is_admin);
    	
    	$dates = $this->getDatesForExcelReport();
    	 
    	$title = "Incoming Bids";
    	
    	\util\ReportHelper::download_excel_file($stats['data'], $incoming_bid_headers, $stats['totals'], $title, $dates);
    }
    
    public function getOutgoingBidsPerTimeAction() {

    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\SellSidePartnerHourlyBids::get_instance())
    	);
    }
    
    public function getOutgoingBidsPerTimeExcelAction() {
    
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	 
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	 
    	$outgoing_bid = \_factory\SellSidePartnerHourlyBids::get_instance();
    	$extra_params = array();
    	$stats	= json_decode($this->getPerTime($outgoing_bid, $extra_params), TRUE);
    	
    	$outgoing_bid_headers = $outgoing_bid->getPerTimeHeader($this->is_admin);
    	
    	$dates = $this->getDatesForExcelReport();
    	 
    	$title = "Outgoing Bids";
    	
    	\util\ReportHelper::download_excel_file($stats['data'], $outgoing_bid_headers, $stats['totals'], $title, $dates);
    }

    public function getContractImpressionsPerTimeAction() {
    	
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\ContractPublisherZoneHourlyImpressions::get_instance())
    	);
    }
    
    public function getContractImpressionsPerTimeExcelAction() {
    	 
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	 
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	 
    	$impressions = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();
    	$stats	= json_decode($this->getPerTime($impressions), TRUE);
    	 
    	$impressions_headers = $impressions->getPerTimeHeader($this->is_admin);
    	 
    	$dates = $this->getDatesForExcelReport();
    	
    	$title = "Contract Impressions";
    	 
    	\util\ReportHelper::download_excel_file($stats['data'], $impressions_headers, $stats['totals'], $title, $dates);
    
    }

    public function getImpressionsCurrentSpendPerTimeAction() {
    	
    	$initialized = $this->initialize();
    	if ($initialized !== true) return $initialized;
    	
    	if (!$this->is_admin):
    		die("bad request");
    	endif;
    	
    	return $this->getResponse()->setContent(
        		$this->getPerTime(\_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance())
    	);
    }

    private function createTotals($data) {
    	 
    	$totals = array();
    	 
    	$counts_holder = array();
    	
    	foreach ($data as $data_obj):
	    	foreach ($data_obj as $name => $value):
	    		if (strpos($value, "%") !== false):
	    			$counts_holder[$name] = empty($counts_holder[$name]) ? 1 : $counts_holder[$name] + 1;
	    			$is_percent = true;
	    		else:
	    			$is_percent = false;
	    		endif;
	    		$value = str_replace(array("$", "%"), array("", ""), $value);
		    	if ($name == "MDYH"):
		    		$totals[$name] = "Totals:";
		    	elseif ((is_numeric($value) || $this->isCpmWord($name)) && strpos($name, "ID") === false):
		    		if ($is_percent === false
		    			&& (($this->isCpmWord($name) && !empty($value)) || !$this->isCpmWord($name))):	
		    			$counts_holder[$name] = empty($counts_holder[$name]) ? 1 : $counts_holder[$name] + 1;
		    		endif;
			    	if (!empty($value)):
			    		$totals[$name]  = empty($totals[$name]) ? $value : $totals[$name] + $value;
			    	elseif (!isset($totals[$name])):
			    		$totals[$name] = "";
			    	endif;
		    	else:
		    		$totals[$name] = "";
		    	endif;
	    	endforeach;
    	endforeach;

    	// format numbers
    	foreach ($totals as $name => $value):
    		$totals[$name] = str_replace(array("$", "%"), array("", ""), $totals[$name]);
	    	if (is_numeric($totals[$name])):
		    	if ($this->isRevWord($name)):
		    		$totals[$name] = "$" . number_format(sprintf("%1.2f", $totals[$name]), 2);
		    	elseif ($this->isPercentWord($name) && isset($counts_holder[$name])):
		    		$totals[$name] =  sprintf("%1.2f", $totals[$name] / $counts_holder[$name]) . '%';
		    	elseif ($this->isCpmWord($name) && isset($counts_holder[$name])):
		    		$totals[$name] = sprintf("%1.7f", $totals[$name] / $counts_holder[$name]);
		    	elseif ($this->isPercentWord($name)):
		    		$totals[$name] = number_format(sprintf("%1.2f", $totals[$name]), 2) . "%";
		    	else:
			    	if (!is_float($totals[$name])):
			    		$totals[$name] = number_format($totals[$name]);
			    	endif;
		    	endif;
	    	endif;
    	endforeach;
    	 
    	return $totals;
    	 
    }
    
    private function isPercentWord($test_word) {
    	$match_words = array(
    			"fill"
    	);
    	return $this->isWordMatch($match_words, $test_word);
    }
    
    private function isRevWord($test_word) {
    	$match_words = array(
    		"revenue",
    		"cost",
    		"revtotal",
    		"spendtotal"
    	);
    	return $this->isWordMatch($match_words, $test_word);
    }
    
    private function isCpmWord($test_word) {
    	$match_words = array(
    			"cpm",
    			"averagebid"
    	);
    	return $this->isWordMatch($match_words, $test_word);
    }
    
    private function isWordMatch($match_words, $test_word) {
    	
    	foreach ($match_words as $match_word):
    		if (strpos(strtolower($test_word), $match_word) !== false):
    			return true;
    		endif;
    	endforeach;
    }
    
    
    private function getPerTime($obj, $extra_params = null) {

		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;

        if ($this->is_admin):
            $this->adminFunctionsSufix = 'Admin';
        endif;


        $params = $this->params()->fromQuery();
        if (!empty($params['step'])):
            $step = $params['step'];
        else:
            $step = 1;
        endif;

        // default 12 hours
		$DateCreatedGreater = date('Y-m-d H:i:s', time() - (12 * 3600 * $step));
		// $DateCreatedGreater = '2010-12-12 12:12:12';
        $DateCreatedLower = date('Y-m-d H:i:s', time() - (12 * 3600 * ($step - 1)));

        if (!empty($params['step'])):

            switch ($params['interval']):

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
            endswitch;
        else:
            $where_params = array(
                'DateCreatedGreater' => $DateCreatedGreater,
                'DateCreatedLower' => $DateCreatedLower,
            );
        endif;
        
        if ($extra_params !== null && count($extra_params) > 0):
        	foreach ($extra_params as $key => $value):
        		$where_params[$key] = $value;
        	endforeach;
        endif;
        
        if (!empty($params['refresh'])):
            $refresh = true;
        else:
            $refresh = false;
        endif;

        $stats_data 	= $obj->getPerTimeCached($this->config_handle, $where_params, 900, $refresh, $this->is_admin);
        $totals_data 	= $this->createTotals($stats_data);
        
        $data = array(
            'data' 		=> $stats_data,
        	'totals' 	=> $totals_data,
            'step' 		=> $step
        );
        
        return json_encode($data);

    }
    
    private function getDatesForExcelReport() {
    	 
    	$params = $this->params()->fromQuery();
    	 
    	if (!empty($params['time_from']) && !empty($params['time_to'])):
    		$dates = $params['time_from'] . ' to ' .  $params['time_to'];
    	else:
	    	if (!empty($params['step'])):
	    		$step = $params['step'];
	    	else:
	    		$step = 1;
	    	endif;
	    
	    	// default 12 hours
	    	$DateCreatedGreater = date('Y-m-d H:i:s', time() - (12 * 3600 * $step));
	    	$DateCreatedLower = date('Y-m-d H:i:s', time() - (12 * 3600 * ($step - 1)));
	    
	    	$dates = $DateCreatedLower . ' to ' .  $DateCreatedGreater;
    	endif;
    	 
    	return $dates;
    }

}
