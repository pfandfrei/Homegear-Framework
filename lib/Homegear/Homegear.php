<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2016
 * a class for local debugging without Homegear PHP extension
 * NOTE: not all methods are supported by now
 */
namespace Homegear;

// create class only if extension does not exist    
if (!class_exists('\Homegear\HomegearException'))
{
    class HomegearException extends \Exception
    {        
    }    
}

// create class only if extension does not exist
if (!class_exists('\Homegear\Homegear'))
{
    // all config has to be done here
    require_once(__DIR__.'/../../config/config.inc.php');
    // the wrapper uses the XMLRPC client from here
    // https://github.com/Homegear/Homegear_PHP_XMLRPC_Client
    require_once(__DIR__.'/../../HM-XMLRPC-Client/Client.php');
    class Homegear
    {
        const LOGFILE = '/var/log/homegear/homegear.api.log';
        
        private static $scriptId = 0;
        private $ssl = false;   // ssl is not yet supported
        private $Client;

        public function __construct()
        {   
            include(__DIR__.'/config.inc.php');
            \Homegear\Homegear::$scriptId++;
            $this->Client = new \XMLRPC\Client($config['host'], $config['port'], $this->ssl);
        }

        // implements Homegear::addEvent
        public function addEvent($event)
        {
            $this->Client->send('addEvent', $event);
        }

        // implements Homegear::removeEvent
        public function removeEvent($eventId)
        {
            $this->Client->send('removeEvent', $eventId);
        }

        // implements Homegear::pollEvent
        public function pollEvent()
        {
            return evaluate($this->Client->send('pollEvent'));
        }
        
        // implements Homegear::getPeerId
        // possible filter values are defined as constants in Constants.php
        public function getPeerId($filterType, $filterValue)
        {
            return $this->Client->send('getPeerId', [$filterType, $filterValue]); 
        }

        // implements Homegear::getAllMetadata (see; https://ref.homegear.eu/rpc.html#getAllMetadata)
        public function getAllMetadata($peerid)
        {
            return $this->Client->send('getAllMetadata', [$peerid]); 
        }

        // implements Homegear::getMetadata (see; https://ref.homegear.eu/rpc.html#getMetadata)
        public function getMetadata($peerid, $meta)
        {
            return $this->Client->send('getMetadata', [$peerid, $meta]); 
        }

        // implements Homegear::setMetadata (see; https://ref.homegear.eu/rpc.html#setMetadata)
        public function setMetadata($peerid, $meta, $value)
        {
            return $this->Client->send('setMetadata', [$peerid, $meta, $value]); 
        }

        // implements Homegear::getValue
        public function getValue($peerId, $channel, $parameterName)
        {
            return $this->evaluate($this->Client->send('getValue', [$peerId, $channel, $parameterName])); 
        }

        // implements Homegear::setValue
        public function setValue($peerId, $channel, $parameterName, $value)
        {
            $this->evaluate( $this->Client->send('setValue', [$peerId, $channel, $parameterName, $value]));
        }

        // implements Homegear::getSystemVariable (see; https://ref.homegear.eu/rpc.html#getSystemVariable)
        public function getSystemVariable($name)
        {
            return $this->evaluate($this->Client->send('getSystemVariable', [$name])); 
        }

        // implements Homegear::setSystemVariable (see; https://ref.homegear.eu/rpc.html#setSystemVariable)
        public function setSystemVariable($name, $value)
        {
            $this->Client->send('setSystemVariable', [$name, $value]); 
        }

        // implements Homegear::deleteSystemVariable (see; https://ref.homegear.eu/rpc.html#deleteSystemVariable)
        public function deleteSystemVariable($name)
        {
            $this->Client->send('deleteSystemVariable', [$name]); 
        }

        // implements Homegear::subscribePeer
        public function subscribePeer($peerId)
        {
            return $this->Client->send('subscribePeer', [$peerId]); 
        }

        // implements Homegear::getParamset
        public function getParamset($peerId, $channel, $type)
        {
            return $this->evaluate($this->Client->send('getParamset', [$peerId, $channel, $type])); 
        }

        // implements Homegear::registerThread
        public function registerThread($peerId)
        {
            return $this->Client->send('registerThread', [$peerId]); 
        }        

        // implements Homegear::getScriptId
        // this is a fake call because scriptId cannot exist when called by XMLRPC
        public function getScriptId()
        {
            return \Homegear\Homegear::$scriptId; 
        }

        // implements Homegear::shuttingDown
        // this is a fake call only
        public function shuttingDown()
        {
            return FALSE;
        }

        // implements Homegear::peerExists
        // this is a fake call only
        public function peerExists($peer)
        {
            return TRUE;
        }

        // implements Homegear::peerExists
        // uses own logfile instead of homegear logfile
        public function log($level, $message)
        {
            error_log($message."\r\n", 3, \Homegear\Homegear::LOGFILE);  
        }

        // internal use to generate HomegearException in case of error
        // because XMLRPC uses error array in case of an error
        private function evaluate($result)
        {
            if (is_array($result) && array_key_exists('faultCode', $result))
            {
                throw new \Homegear\HomegearException;
            }

            return $result;
        }
    }
}