<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the Motion class for Homematic HM-Sec-RHS
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class RotarySwitch extends Device
{    
    const TYPE_STRING = 'HM-Sec-RHS';
    
    const CLOSED    = 0;
    const TILT      = 1;
    const OPENED    = 2;
    
    public function __construct($peerid)
    {
        parent::__construct($peerid);
    }

    /* 
     * gets current state
     */
    function getState()
    {
        global $api;
        $result = $api->getValue($this->peerid, 1, 'STATE', FALSE);
        return $result;
    }
}
