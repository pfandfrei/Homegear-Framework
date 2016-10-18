<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the HMInput class for Homematic HM-SCI-3-FM
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class HMInput extends Device
{    
    const TYPE_STRING = 'HM-SCI-3-FM';
    
    private $number_of_channels;
    
    public function __construct($peerid, $number_of_channels = 3)
    {
        parent::__construct($peerid);
        $this->number_of_channels = $number_of_channels;
    }

    /* 
     * gets current state
     */
    function getState($chn_no = 1)
    {
        $result = FALSE;
        if ($chn_no > 0 && $chn_no <= $this->number_of_channels)
        {
            global $api;
            $result = $api->getValue($this->peerid, $chn_no, 'STATE', FALSE);
        }
        return $result;
    }
}
