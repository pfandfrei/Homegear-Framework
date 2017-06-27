<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2016
 * the Api class catches some possible exceptions and adds some default values
 * NOTE: not all methods are supported by now
 */

namespace Homegear;

include_once __DIR__ . '/config.inc.php';
include_once __DIR__ . '/Constants.php';
include_once __DIR__ . '/Homegear.php';

date_default_timezone_set($config['tz']);
// define $api for global access
$api = new \Homegear\Api();

class Api
{
    const LOGFILE = '/var/log/homegear/homegear.api.log';
    
    private $_hg;

    public function __construct()
    {
        $this->_hg = new \Homegear\Homegear();
    }

    /*
     * implements Homegear::getSystemVariable (see; https://ref.homegear.eu/rpc.html#getSystemVariable)
     * returns given default value (or FALSE) in case of an error
     */
    public function getSystemVariable($name, $default = FALSE)
    {
        try
        {
            $result = $this->_hg->getSystemVariable($name);
        }
        catch (\Homegear\HomegearException $e)
        {
            $result = $default;
        }
        return $result;
    }
    
    /*
     * implements Homegear::setSystemVariable (see; https://ref.homegear.eu/rpc.html#setSystemVariable)
     */
    public function setSystemVariable($name, $value)
    {
        try
        {
            $result = $this->_hg->setSystemVariable($name, $value);
        }
        catch (\Homegear\HomegearException $e)
        {
            
        }
    }
    
    /*
     * implements Homegear::deleteSystemVariable (see; https://ref.homegear.eu/rpc.html#deleteSystemVariable)
     */
    public function deleteSystemVariable($name)
    {
        try
        {
            $result = $this->_hg->deleteSystemVariable($name);
        }
        catch (\Homegear\HomegearException $e)
        {
            
        }
    }
    
    /*
     * implements Homegear::getValue
     * returns given default value (or FALSE) in case of an error
     */
    public function getValue($peerId, $channel, $param, $default = FALSE)
    {
        try
        {
            $result = $this->_hg->getValue(intval($peerId), intval($channel), $param);
        }
        catch (\Homegear\HomegearException $e)
        {
            $result = $default;
        }
        return $result;
    }

    /*
     * implements Homegear::setValue
     * returns error code in case of an error
     */
    public function setValue($peerId, $channel, $param, $value)
    {
        $result = 0;

        try
        {
            $result = $this->_hg->setValue(intval($peerId), intval($channel), $param, $value);
        }
        catch (\Homegear\HomegearException $e)
        {
            $result = $e->getCode();
        }

        return $result;
    }

    /*
     * implements Homegear::getPeerId
     * possible filter values are defined as constants in Constants.php
     * returns FALSE if no peer can be found
     */
    public function getPeerId($deviceName, $filter = \Homegear\Constants\GetPeerId::Filter_DeviceName)
    {
        $result = FALSE;
        $device = $this->_hg->getPeerId($filter, $deviceName);
        switch (count($device))
        {
            case 0:
                break;
            case 1:
                $result = $device[0];
                break;
            default:
                $result = $device;
                break;
        }
        return $result;
    }

    /*
     * implements Homegear::getAllMetadata (see; https://ref.homegear.eu/rpc.html#getAllMetadata)
     * returns associative array with meta data (e.g. NAME)
     * returns FALSE if peer  does not exist
     */
    public function getAllMeta($peerid)
    {
        $result = FALSE;
        try
        {
            $result = $this->_hg->getAllMetadata(intval($peerid));        
        }
        catch (\Homegear\HomegearException $e)
        {
        }
        return $result;
    }

    /* 
     * get meta data entry
     * returns FALSE if peer or meta data does not exist
     */
    public function getMeta($peerid, $meta, $default = FALSE)
    {
        $result = $default;
        try
        {
            $result = $this->_hg->getMetadata(intval($peerid), $meta);
        }
        catch (\Homegear\HomegearException $e)
        {
            $result = $default;
        }
        return $result;
    }

    /*
     * implements Homegear::setMetadata (see; https://ref.homegear.eu/rpc.html#setMetadata)
     */
    public function setMeta($peerid, $meta, $value)
    {
        $this->_hg->setMetadata(intval($peerid), $meta, $value);
    }

    /*
     * implements a shortcut for getPeerId with filter Filter_DeviceName
     */
    public function getDeviceByName($deviceName)
    {
        return $this->getPeerId($deviceName, \Homegear\Constants\GetPeerId::Filter_DeviceName);
    }

    /*
     * implements a shortcut for getPeerId with filter Filter_DeviceType
     */
    public function getDeviceByType($deviceName)
    {
        return $this->getPeerId($deviceName, \Homegear\Constants\GetPeerId::Filter_DeviceType);
    }

    /*
     * get the name of the peer stored in meta data
     */
    public function getName($peerid)
    {
        return $this->getMeta(intval($peerid), 'NAME', $peerid);
    }

    /*
     * get the friendlay name of the peer as given in meta data
     */
    public function getFriendly($peerid)
    {
        return $this->getMeta(intval($peerid), 'FRIENDLY_NAME', $peerid);
    }

    /*
     * implements Homegear::addEvent
     */
    public function addEvent($event)
    {
        return $this->_hg->addEvent($event);
    }

    /*
     * implements Homegear::addEvent
     */
    public function enableEvent($event, $enabled)
    {
        return $this->_hg->enableEvent($event, $enabled);
    }

    /*
     * implements Homegear::removeEvent
     * catches exception if event does not exist
     */
    public function removeEvent($eventId)
    {
        $result = TRUE;
        try
        {
            $this->_hg->removeEvent($eventId);
        }
        catch (\Homegear\HomegearException $e)
        {
            $result = FALSE;
        }
        return $result;
    }
    
    public function log($level, $message)
    {
        try
        {
            error_log($message."\r\n", 3, \Homegear\Api::LOGFILE); 
        } 
        catch (Exception $ex) 
        {
        }
    }
}
