<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class PublisherAdZone  implements InputFilterAwareInterface
{
    public $PublisherAdZoneID;
    public $PublisherWebsiteID;
    public $PublisherAdZoneTypeID;
    public $ImpressionType;
    public $AdName;
    public $Description;
    public $PassbackAdTag;
    public $AdStatus;
    public $AutoApprove;
    public $AdTemplateID;
    public $IsMobileFlag;
    public $Width;
    public $Height;
    public $FloorPrice;
    public $TotalRequests;
    public $TotalImpressionsFilled;
    public $TotalAmount;
    public $DateCreated;
    public $DateUpdated;
    public $AdOwnerID;
    protected $inputFilter;

    public function exchangeArray($data)
    {
    	$this->AdName          			= (isset($data['AdName']))          		? $data['AdName']           	: null;
        $this->PublisherAdZoneTypeID    = (isset($data['PublisherAdZoneTypeID']))   ? $data['PublisherAdZoneTypeID'] : null;
        $this->ImpressionType    		= (isset($data['ImpressionType']))   		? $data['ImpressionType'] 		: null;
        $this->Description     			= (isset($data['Description']))     		? $data['Description']      	: null;
        $this->PassbackAdTag   			= (isset($data['PassbackAdTag']))   		? $data['PassbackAdTag']    	: null;
        $this->AdTemplateID    			= (isset($data['AdTemplateID']) && $data['AdTemplateID'] != "") ? $data['AdTemplateID']     : null;
        $this->IsMobileFlag    			= (isset($data['IsMobileFlag']))    		? $data['IsMobileFlag']     	: null;
        $this->FloorPrice      			= (isset($data['FloorPrice']))      		? $data['FloorPrice']       	: null;
        $this->Width           			= (isset($data['Width']))           		? $data['Width']            	: null;
        $this->Height          			= (isset($data['Height']))          		? $data['Height']           	: null;
        $this->AdOwnerID       			= (isset($data['AdOwnerID']))       		? $data['AdOwnerID']        	: null;
    }

    public function getArrayCopy()
    {
    	return get_object_vars($this);
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
     * @link http://framework.zend.com/manual/2.0/en/user-guide/forms-and-actions.html
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    	throw new \Exception("Not used");
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\InputFilter\InputFilterAwareInterface::getInputFilter()
     * @link http://framework.zend.com/manual/2.0/en/user-guide/forms-and-actions.html
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter):
        
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'PublisherAdZoneTypeID',
            		'required' => false,
            		'filters' => array(
            				array('name' => 'Int'),
            		),
            )));    

            $inputFilter->add($factory->createInput(array(
            		'name' => 'ImpressionType',
            		'required' => true,
            		'filters' => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            	'name' => 'AdName',
                'required' => true,
                'filters' => array(
            	   array('name' => 'StripTags'),
                   array('name' => 'StringTrim'),
                ),
                'validators' => array(
                		array(
                				'name' => 'StringLength',
                				'options' => array(
                						'min' => 1,
                						'max' => 100,
                				),
                		),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'Description',
            		'required' => true,
            		'filters' => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'AdTemplateID',
            		'required' => false,
            		'filters' => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'IsMobileFlag',
            		'required' => true,
            		'filters' => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'Width',
            		'required' => false,
            		'filters' => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'Height',
            		'required' => false,
            		'filters' => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'FloorPrice',
            		'required' => true,
            		'filters' => array(
            				array('name' => 'NumberFormat'),
            		),
            )));
            
            $this->inputFilter = $inputFilter;
        endif;
        
        return $this->inputFilter;
    }
}
?>