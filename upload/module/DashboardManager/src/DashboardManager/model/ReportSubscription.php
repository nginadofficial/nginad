<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace model;

class ReportSubscription {
    public $ReportSubscriptionID;
    public $UserID;
    public $Status;
    public $DateCreated;
    public $DateUpdated;
    
    public function initialize($data) {
        
        foreach ($data as $key=>$val){
            $this->$key = $val;
        }
        
    }
}

?>