<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the Motion class for Homematic HM-Sen-LI-O
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class Luxmeter extends Device
{    
    const TYPE_STRING = 'HM-Sen-LI-O';
    
    public function __construct($peerid)
    {
        parent::__construct($peerid);
    }

    /* 
     * get actual brightness
     */
    function getBrightness()
    {
        global $api;
        return floatval($api->getValue($this->peerid, 1, 'LUX', 0.0));
    }
}
