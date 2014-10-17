<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace WebsiteCustom\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

	/**
	 * The default action - show the home page
	 */
	public function termsAction() {
		
		$legal_text = 'module/WebsiteCustom/view/websitecustom/terms.txt';
		 
		$view = new ViewModel(array(
				'legal_text' => file_get_contents($legal_text),
		));
				
		
		return $view->setTemplate('websitecustom/bottomlegal.phtml');
	}
	
	public function privacyAction() {
	
		$legal_text = 'module/WebsiteCustom/view/websitecustom/privacy.txt';
			
		$view = new ViewModel(array(
				'legal_text' => file_get_contents($legal_text),
		));
	
	
		return $view->setTemplate('websitecustom/bottomlegal.phtml');
	}

	public function publisheragreementAction() {
	
		$legal_text = 'module/WebsiteCustom/view/websitecustom/publisheragreement.txt';
			
		$view = new ViewModel(array(
				'legal_text' => file_get_contents($legal_text),
		));
	
	
		return $view->setTemplate('websitecustom/bottomlegal.phtml');
	}
	
	public function demandagreementAction() {
	
		$legal_text = 'module/WebsiteCustom/view/websitecustom/demandagreement.txt';
			
		$view = new ViewModel(array(
				'legal_text' => file_get_contents($legal_text),
		));
	
	
		return $view->setTemplate('websitecustom/bottomlegal.phtml');
	}
	

}
