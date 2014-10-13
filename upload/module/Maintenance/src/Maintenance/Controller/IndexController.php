<?php

/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace Maintenance\Controller;

/*
 * Special case for Mike's environment where
 * Composer is not autoloading PHPOffice for 
 * whatever reason.
 */

//if (!class_exists('\\PHPExcel')):
//	require('vendor/PHPOffice/PHPExcel/Classes/PHPExcel.php');
//endif;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mail\Message;
use Zend\Mime;
use PHPExcel_IOFactory;
use util\Maintenance;

class IndexController extends AbstractActionController {

    public function indexAction() {
        echo "NGINAD MAINTENANCE<br />\n";
        exit;
    }

    /*
     * This method should be hooked up to a minutely cron job.
     * It will only run the maintenance for each interval 
     * at it's scheduled time no matter how many times this 
     * Controller is called from a cron tab.
     * 
     * Ex: 
     */

    public function crontabAction() {

        $config = $this->getServiceLocator()->get('Config');

        $secret_key = $this->getRequest()->getQuery('secret_key');

        if ($secret_key != $config['maintenance']['secret_key_crontab']):
            die("Permission Denied");
        endif;

        foreach ($config['maintenance']['tasks'] as $tagname => $maintenance_element):
            $interval_in_minutes = $maintenance_element['interval_in_minutes'];
            $should_run_maintenance = \util\Maintenance::checkRunMaintenance($tagname, $interval_in_minutes);

            if ($should_run_maintenance === true):
                $maintenance_function = $maintenance_element['maintenance_function'];
                $this->$maintenance_function();
            endif;
        endforeach;

        echo "NGINAD MAINTENANCE<br />\n";
        exit;
    }

    public function excelAction() {
        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        // Create new PHPExcel object
        echo date('H:i:s'), " Create new PHPExcel object", EOL;
        $objPHPExcel = new \PHPExcel();

        var_dump($objPHPExcel);
        echo "EXCEL MAINTENANCE ACTION<br />\n";
        exit;
    }

    public function dailyAction() {
    	
        $date = date('Y-m-d', time() - 60 * 60 * 24);
        $config = $this->getServiceLocator()->get('Config');

        $ReportSubscription = \_factory\ReportSubscription::get_instance();
        $subscribed_users = $ReportSubscription->get(array(
            'Status' => 1
        ));

        $transport = $this->getServiceLocator()->get('mail.transport');

        $where_params = array(
        	//'DateCreatedGreater' => $date . ' 00:00:00',
        	'DateCreatedGreater' => '2012-12-12 00:00:00',
            'DateCreatedLower' => $date . ' 23:59:59',
        );

        $letters = range('A', 'Z');
        
        $publisher_impression_factory 	= \_factory\PublisherImpressionsAndSpendHourly::get_instance();
        $demand_impression_factory 		= \_factory\DemandImpressionsAndSpendHourly::get_instance();
        $authUsersFactory 				= \_factory\authUsers::get_instance();
        
        foreach ($subscribed_users as $subscribed_user):
        	
            $is_admin = ($subscribed_user['user_role'] == 1) ? TRUE : FALSE;

            if ($is_admin):
            	continue;
            endif;
            
            $params = array();
            
            $where_params = array(
            		'DateCreatedGreater' => $date . ' 00:00:00',
            		//'DateCreatedGreater' => '2012-12-12 00:00:00',
            		'DateCreatedLower' => $date . ' 23:59:59',
            );
            
            $params["user_id"]	= $subscribed_user->UserID;
            $authUsers			= $authUsersFactory->get_row($params);

            if ($authUsers->PublisherInfoID != null):
	            $where_params['PublisherInfoID'] = $authUsers->PublisherInfoID;
	            $impressions = json_decode($this->getPerTime($publisher_impression_factory, $config, $is_admin, $where_params), TRUE)['data'];
	            $impressions_header = $publisher_impression_factory->getPerTimeHeader($is_admin);
	        elseif ($authUsers->DemandCustomerInfoID != null):
	        	$where_params['DemandCustomerInfoID'] = $authUsers->DemandCustomerInfoID;
	        	$impressions = json_decode($this->getPerTime($demand_impression_factory, $config, $is_admin, $where_params), TRUE)['data'];
	        	$impressions_header = $demand_impression_factory->getPerTimeHeader($is_admin);
            endif;

            $text = new Mime\Part('Excel');
            $text->type = Mime\Mime::TYPE_TEXT;
            $text->charset = 'utf-8';

            $objPHPExcel = new \PHPExcel();

            $objPHPExcel->getProperties()->setCreator("nginad.com")
                    ->setLastModifiedBy("nginad.com")
                    ->setTitle("Statistic reports")
                    ->setSubject("")
                    ->setDescription("")
                    ->setKeywords("")
                    ->setCategory("");

            
//            
//            Strat first sheet


                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getSheet(0)->setTitle('Impressions');
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Impressions stats for ' . $date);

                $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $letters[count($impressions_header) - 1] . '1');



                for ($i = 0; $i < count($impressions_header); $i++) {
                    $objPHPExcel->getActiveSheet()->SetCellValue($letters[$i] . '3', $impressions_header[$i]);
                }

                if (empty($impressions)) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
                    $objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($impressions_header) - 1] . '5');
                } else {
                    $i = 6;
                    foreach ($impressions as $impression) {

                        $impression = array_values((array) ($impression));
                        for ($j = 0; $j < count($impression); $j++) {
                            $objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $impression[$j]);
                        }

                        $i++;
                    }
                }

                foreach ($letters as $columnID) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                            ->setAutoSize(true);
                }

            //header('Content-type: application/vnd.ms-excel');
            //header('Content-Disposition: attachment; filename="file.xls"');

            $objPHPExcel->setActiveSheetIndex(0);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            ob_start();
            $objWriter->save('php://output');
            $fileContent = ob_get_contents();
            ob_end_clean();

            $attachment = new Mime\Part($fileContent);
            $attachment->type = 'application/vnd.ms-excel';
            $attachment->filename = 'statistic-' . $date . '.xls';
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $attachment->type = 'application/octet-stream';
            $attachment->encoding = Mime\Mime::ENCODING_BASE64;

            $mimeMessage = new Mime\Message();
            $mimeMessage->setParts(array($text, $attachment));
            $message = new Message();
			//$message->addTo('test@example.com')
     		$message->addTo($subscribed_user['user_email'])
//                    ->addFrom('noreply@nginad.com')
                    ->addFrom($config['mail']['reply-to']['email'], $config['mail']['reply-to']['name'])
                    ->setSubject('Statistic for ' . $date)
                    ->setBody($mimeMessage);
            $transport->send($message);
//            die();
        endforeach;

        exit(1);
    }

    public function dailyMaintenanceAction() {
        $this->dailyAction();
    }

    public function tenMinuteMaintenanceAction() {

        /*
         * update all compiled stats into the AdCampaignBanner table
         */

        $BidTotalsRollupFactory = \_factory\BidTotalsRollup::get_instance();
        $ImpressionAndSpendTotalsRollupFactory = \_factory\ImpressionAndSpendTotalsRollup::get_instance();

        $AdCampaignBannerFactory = \_factory\AdCampaignBanner::get_instance();
        $params = array();
        $params["Active"] = 1;
        $AdCampaignBannerList = $AdCampaignBannerFactory->get($params);

        foreach ($AdCampaignBannerList as $AdCampaignBanner):

            $banner_id = $AdCampaignBanner->AdCampaignBannerID;

            $params = array();
            $params["AdCampaignBannerID"] = $banner_id;
            $BidTotalsRollup = $BidTotalsRollupFactory->get_row($params);
            if ($BidTotalsRollup == null):
                continue;
            endif;
            $ImpressionAndSpendTotalsRollup = $ImpressionAndSpendTotalsRollupFactory->get_row($params);
            if ($ImpressionAndSpendTotalsRollup == null):
                continue;
            endif;

            $AdCampaignBanner->BidsCounter = $BidTotalsRollup->TotalBids;
            $AdCampaignBanner->ImpressionsCounter = $ImpressionAndSpendTotalsRollup->TotalImpressions;
            $AdCampaignBanner->CurrentSpend = $ImpressionAndSpendTotalsRollup->TotalSpendGross;

            $data = $AdCampaignBanner->getArrayCopy();

            $AdCampaignBannerFactory->saveAdCampaignBannerFromDataArray($data);

        endforeach;
    }
    
    private function getPerTime($obj, $config, $is_admin, $extra_params = null) {
    
    
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
    
    	if ($extra_params !== null && count($extra_params) > 0):
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
    			// 'data' => $obj->getPerTimeCached($config, $where_params, 900, $refresh, $is_admin),
    			'data' => $obj->getPerTime($where_params),
    			'step' => $step
    	);
    
    	return json_encode($data);
    
    }

}
