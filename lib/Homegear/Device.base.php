<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2016
 * the Device base class catches some possible exceptions and adds some default values
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/Api.php';

abstract class Device
{    
    protected $peerid = 0;
    protected $name = '';
    protected $friendly = '';

    /*
     * creates devices and requests name and friendly name
     * in: peer id or peer name
     */
    public function __construct($peerid)
    {
        global $api;
        if (!is_numeric($peerid))
        {
            $this->peerid = $api->getPeerId($peerid);
        }
        else
        {
            $this->peerid = $peerid;
        }
        
        $meta = $api->getAllMeta($this->peerid);
        $this->name = $meta['NAME']? : $peerid;
        if (array_key_exists('FRIENDLY_NAME', $meta))
        {
            $this->friendly = $meta['FRIENDLY_NAME'];
        }
        else
        {
            $this->friendly = $peerid;
        }
    }
    
    /*
     * gets name of device
     */
    public function getName()
    {
        global $api;
        return $api->getName($this->peerid);
    }
    
    /*
     * gets friendly name of device
     */
    public function getFriendly()
    {
        global $api;
        return $api->getFriendly($this->peerid);
    }
    
    /* 
     * returns true if low battery is signaled
     */
    public function LowBat()
    {
        global $api;
        return $api->getValue($this->peerid, 0, 'LOWBAT', FALSE);
    }
    
    /* 
     * returns true if sticky unreach is signaled
     */
    public function StickyUnreach()
    {
        global $api;
        return $api->getValue($this->peerid, 0, 'STICKY_UNREACH', FALSE);
    }
    
    /* 
     * returns true if config pending is signaled
     */    
    public function ConfigPending()
    {
        global $api;
        return $api->getValue($this->peerid, 0, 'CONFIG_PENDING', FALSE);
    }
    
    /* 
     * writes a log entry including current date/time and name of device
     */
    public function Log($message)
    {
        global $api;
        $now = strftime('%Y-%m-%d %H:%M:%S');      
        $parts = explode('\\', get_class($this));
        $class_name = array_pop($parts);
        $api->log(2, $now." >> [".$class_name."] ".$this->name." ".$message);  
    }
}
