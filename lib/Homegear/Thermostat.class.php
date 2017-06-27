<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the Motion class for Homematic HM-TC-IT-WM-W-EU
 * NOTE: not all methods are supported by now
 * 
 * to compare actual temp with previous value
 * call getLastTemparature() before getActualTemperature()
 * same for humidity
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class Thermostat extends Device
{        
    const TYPE_STRING = 'HM-TC-IT-WM-W-EU';
    
    const LAST_HUMIDITY = 'LAST_HUMIDITY';
    const LAST_TEMPERATURE = 'LAST_TEMPERATURE';
    
    public function __construct($peerid)
    {
        parent::__construct($peerid);
    }

    /* 
     * get actual temperature
     */
    function getActTemperature()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'ACTUAL_TEMPERATURE', 0);
        return $result;
    }

    /* 
     * get actual humidity
     */
    function getActHumidity()
    {
        global $api;
        $result = $api->getValue($this->peerid, 2, 'ACTUAL_HUMIDITY', 0);
        return $result;
    }

    /* 
     * get previous temperature
     */
    function getLastTemperature()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\Thermostat::LAST_TEMPERATURE, 0);
    }

    /* 
     * get previous humidity
     */
    function getLastHumidity()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\Thermostat::LAST_HUMIDITY, 0);
    }

    /* 
     * set previous temperature
     */
    function setLastTemperature($value)
    {
        global $api;
        $result = $api->setMeta($this->peerid, \Homegear\Thermostat::LAST_TEMPERATURE, $value);
    }

    /* 
     * set previous humidity
     */
    function setLastHumidity($value)
    {
        global $api;
        $result = $api->setMeta($this->peerid, \Homegear\Thermostat::LAST_HUMIDITY, $value);
    }
}
