<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the Motion class for Homematic HM-Sen-MDIR-O-2
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class Motion extends Device
{    
    const TYPE_STRING = 'HM-Sen-MDIR-O-2';
    
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
        return intval($api->getValue($this->peerid, 1, 'BRIGHTNESS', 0));
    }
}
