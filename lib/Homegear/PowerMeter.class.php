<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the PowerMeter class for Homematic HM-ES-PMSw1-Pl
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class PowerMeter extends Device
{    
    const TYPE_STRING = 'HM-ES-PMSw1-Pl';
    
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
    }

    /*
     * set state 
     */
    function setState($state, $ontime)
    {
        global $api;
        $api->setValue($this->peerid, 1, 'STATE', boolval($state));
    }
    
    /* 
     * gets current 
     */
    function getCurrent()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'CURRENT', FALSE);
    }
    
    /* 
     * gets current 
     */
    function getEnergyCounter()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'ENERGY_COUNTER', FALSE);
    }
    
    /* 
     * gets current 
     */
    function getFrequency()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'FREQUENCY', FALSE);
    }
    
    /* 
     * gets current 
     */
    function getPower()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'POWER', FALSE);
    }
    
    /* 
     * gets current 
     */
    function getVoltage()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'VOLTAGE', FALSE);
    }
}
