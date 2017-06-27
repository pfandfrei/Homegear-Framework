<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the DoorContact class for Homematic HM-Sec-SCo
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class DoorContact extends Device
{    
    const TYPE_STRING = 'HM-Sec-SCo';
    
    const CLOSED    = 0;
    const OPENED    = 1;
    
    /*
     * creates devices and requests name and friendly name
     * in: peer id or peer name
     */
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

    /* 
     * returns true if devices signals OPENED
     */
    function isOpen()
    {
        return boolval($this->getState());
    }
}
