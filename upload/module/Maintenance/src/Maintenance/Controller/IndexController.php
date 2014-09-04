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
        $subscibe_users = $ReportSubscription->get(array(
            'Status' => 1,
            'user_role' => 1,
        ));

        $transport = $this->getServiceLocator()->get('mail.transport');

        $where_params = array(
            'DateCreatedGreater' => $date . ' 00:00:00',
//            'DateCreatedGreater' => '2013-12-12 00:00:00',
            'DateCreatedLower' => $date . ' 23:59:59',
        );

        $letters = range('A', 'Z');
        
        foreach ($subscibe_users as $one) {
            $is_admin = ($one['user_role'] != 1) ? TRUE : FALSE;

            $impressions_obj = \_factory\BuySideHourlyImpressionsByTLD::get_instance();
            $impressions = $impressions_obj->getPerTimeCached($config, $where_params, 900, TRUE, $is_admin);
            $impressions_header = $impressions_obj->getPerTimeHeader($is_admin);

            $incoming_bids_obj = \_factory\BuySideHourlyBidsCounter::get_instance();
            $incoming_bids = $incoming_bids_obj->getPerTimeCached($config, $where_params, 900, TRUE, $is_admin);
            $incoming_bids_header = $incoming_bids_obj->getPerTimeHeader($is_admin);

            $outgoing_bids_obj = \_factory\SellSidePartnerHourlyBids::get_instance();
            $outgoing_bids = $outgoing_bids_obj->getPerTimeCached($config, $where_params, 900, TRUE, $is_admin);
            $outgoing_bids_header = $outgoing_bids_obj->getPerTimeHeader($is_admin);

            $contracts_obj = \_factory\ContractPublisherZoneHourlyImpressions::get_instance();
            $contracts = $contracts_obj->getPerTimeCached($config, $where_params, 900, TRUE, $is_admin);
            $contracts_header = $contracts_obj->getPerTimeHeader($is_admin);

            $current_spend_obj = \_factory\BuySideHourlyImpressionsCounterCurrentSpend::get_instance();
            $current_spend = $current_spend_obj->getPerTimeCached($config, $where_params, 900, TRUE, $is_admin);
            $current_spend_header = $current_spend_obj->getPerTimeHeader($is_admin);

            $text = new Mime\Part('Hi!');
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



            for($i = 0; $i < count($impressions_header); $i++){
                $objPHPExcel->getActiveSheet()->SetCellValue($letters[$i] . '3', $impressions_header[$i]);
            }

            if (empty($impressions)) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
                $objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($impressions_header) - 1] . '5');
            } else {
                $i = 5;
                foreach ($impressions as $impression) {
                    
                    $impression = array_values((array)($impression));
                    for ($j = 0; $j < count($impressions_header); $j++) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $impression[$j]);
                    }

                    $i++;
                }
            }


//            
//            Strat second sheet
//            
            $objPHPExcel->createSheet(1);
            $objPHPExcel->getSheet(1)->setTitle('Incoming bids');
            $objPHPExcel->setActiveSheetIndex(1);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Incoming bids stats for ' . $date);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $letters[count($incoming_bids_header) - 1] . '1');
            
            for($i = 0; $i < count($incoming_bids_header); $i++){
                $objPHPExcel->getActiveSheet()->SetCellValue($letters[$i] . '3', $incoming_bids_header[$i]);
            }
            
            if (empty($incoming_bids)) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
                $objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($incoming_bids_header) - 1] . '5');
            } else {
                $i = 5;
                foreach ($incoming_bids as $bid) {
                    $bid = array_values((array)($bid));
                    for ($j = 0; $j < count($incoming_bids_header); $j++) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $bid[$j]);
                    }
                    $i++;
                }
            }



//            
//            Strat third sheet
//            
            $objPHPExcel->createSheet(2);
            $objPHPExcel->getSheet(2)->setTitle('Outgoing bids');
            $objPHPExcel->setActiveSheetIndex(2);

            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Outgoing bids stats for ' . $date);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $letters[count($outgoing_bids_header) - 1] . '1');

            for($i = 0; $i < count($outgoing_bids_header); $i++){
                $objPHPExcel->getActiveSheet()->SetCellValue($letters[$i] . '3', $outgoing_bids_header[$i]);
            }

            if (empty($outgoing_bids)) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
                $objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($outgoing_bids_header) - 1] . '5');
            } else {
                $i = 5;
                foreach ($outgoing_bids as $bid) {

                    $bid = array_values((array)($bid));
                    for ($j = 0; $j < count($outgoing_bids_header); $j++) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $bid[$j]);
                    }
                    $i++;
                }
            }


//            
//            Strat fourth sheet
//            
            $objPHPExcel->createSheet(3);
            $objPHPExcel->getSheet(3)->setTitle('Contract impressions');
            $objPHPExcel->setActiveSheetIndex(3);

            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Contract impressions stats for ' . $date);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $letters[count($contracts_header) - 1] . '1');

            for($i = 0; $i < count($contracts_header); $i++){
                $objPHPExcel->getActiveSheet()->SetCellValue($letters[$i] . '3', $contracts_header[$i]);
            }

            if (empty($contracts)) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
                $objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($contracts_header) - 1] . '5');
            } else {
                $i = 5;
                foreach ($contracts as $contract) {

                    $contract = array_values((array)($contract));
                    for ($j = 0; $j < count($contracts_header); $j++) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $contract[$j]);
                    }
                    $i++;
                }
            }
//            
//            Strat fifth sheet
//            
            $objPHPExcel->createSheet(4);
            $objPHPExcel->getSheet(4)->setTitle('Current spend');
            $objPHPExcel->setActiveSheetIndex(4);

            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Current spend stats for ' . $date);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $letters[count($current_spend_header) - 1] . '1');

            for($i = 0; $i < count($current_spend_header); $i++){
                $objPHPExcel->getActiveSheet()->SetCellValue($letters[$i] . '3', $current_spend_header[$i]);
            }

            if (empty($current_spend)) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
                $objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($current_spend_header) - 1] . '5');
            } else {
                $i = 5;
                foreach ($current_spend as $impression) {

                    $impression = array_values((array)($impression));
                    for ($j = 0; $j < count($current_spend_header); $j++) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $impression[$j]);
                    }
                    $i++;
                }
            }


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
            $message->addTo('michael.mitrofanov@gmail.com')
//            $message->addTo($one['user_email'])
//                    ->addFrom('noreply@nginad.com')
                    ->addFrom($config['mail']['reply-to']['email'], $config['mail']['reply-to']['name'])
                    ->setSubject('Statistic for ' . $date)
                    ->setBody($mimeMessage);
            $transport->send($message);
//            die();
        }
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

}
