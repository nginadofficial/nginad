<?php

namespace DashboardManager\Controller;

use DashboardManager\ParentControllers\DemandAbstractActionController;

class ServicesController extends DemandAbstractActionController {
	
	public function sspdirectoryAction() {
	
		$initialized = $this->initialize();
		if ($initialized !== true) return $initialized;
	
		
		
		$data = array("foo" => "bar");

		$this->setJsonHeader();
		return $this->getResponse()->setContent(json_encode($data));
	
	}
	
}

?>