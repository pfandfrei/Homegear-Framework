<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the HMSwitch class for Homematic HM-LC-Sw1PBU-FM
 * NOTE: not all methods are supported by now
 * 
 * should also work with HM-LC-Sw2PBU-FM 
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class HMSwitch extends Device
{    
    const TYPE_STRING = 'HM-LC-Sw1PBU-FM';

    const LAST_STATE = 'LAST_STATE';
    
    private $number_of_channels;
    private $retry = 0;
    
    /*
     * init with 2 channels for HM-LC-Sw2PBU-FM (not tested)
     */
    public function __construct($peerid, $number_of_channels = 1)
    {
        parent::__construct($peerid);
        $this->number_of_channels = $number_of_channels;
    }

    /*
     * set repeat of sending in device is not reachable
     */
    function setRetry($retry)
    {
        $this->retry = $retry;
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
            $this->setLastState($result);
        }
        return $result;
    }

    /*
     * set state for specific time
     * will retry in case of error
     */
    function setState($state, $ontime = 0, $chn_no = 1)
    {
        if ($chn_no > 0 && $chn_no <= $this->number_of_channels)
        {
            $count = 0;
            
            global $api;
            do
            {
                $result = 0;
                if ($ontime > 0)
                {
                    $result += $api->setValue($this->peerid, $chn_no, 'ON_TIME', floatval($ontime));
                }
                $result += $api->setValue($this->peerid, $chn_no, 'STATE', boolval($state));

                $this->Log("set ".($state ? "ON" : "OFF")." for ".$ontime."s");
            }
            while ($result && (++$count < $this->retry));
        }
    }
    
    /*
     * get last state
     */
    function getLastState()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\HMSwitch::LAST_STATE, false);
    }
    
    /*
     * set last state
     */
    function setLastState($value)
    {
        global $api;
        $result = $api->setMeta($this->peerid, \Homegear\HMSwitch::LAST_STATE, boolval($value));
    }
}
