<?php

/*
 * Homegear wrapper V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2016
 * the Blind class for Homematic HM-LC-Bl1PBU-FM
 * 
 * Blind uses to different terms 
 * - LEVEL (0,0 ... 1.0) as used by Homematic 
 *     decribes how much the blind is opened
 *     the blind is fully closed with a value of 0.0
 * - POSITION (0 ... 100) as used by ms ;-)
 *     describes how much the blind is closed
 *     the blind is fully closed with a value of 100
 * 
 * MODE, LAST_TIME, HOLD_TIME is used for automatic shadowing on sunny summer days 
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';
include_once __DIR__ . '/Constants.php';

class Blind extends Device
{

    const TYPE_STRING = 'HM-LC-Bl1PBU-FM';
    const MANUAL = 0x01;    // manual mode
    const AUTO = 0x02;      // automatic mode
    const HOLD_TIME = 'HOLD';           // meta tag for hold time
    const LAST_LEVEL = 'LAST_LEVEL';   // meta tag for last level
    const TIMESTAMP = 'LAST_TIME';      // meta tag for last time
    const MODE = 'MODE';           // meta tag for mode

    private $retry = 0;
    private $autorepeat = FALSE;

    public function __construct($peerid)
    {
        parent::__construct($peerid);
    }

    /*
     * set repeat of sending in device is not reachable
     */
    function setRetry($retry)
    {
        $this->retry = $retry;
    }

    /*
     * set repeat of sending in device is not reachable
     */
    function setAutoRepeat($autorepeat)
    {
        $this->autorepeat = $autorepeat;
    }

    /*
     * get actual blind position
     * see remark in header of this file
     */
    function getPosition()
    {
        return $this->Level2Position($this->getLevel());
    }

    /*
     * set actual blind position
     * set movement mode 
     * see remark in header of this file
     */

    function setPosition($pos, $manual = false)
    {
        $this->setLevel($this->peerid, $this->Position2Level($pos), $manual);
    }

    /*
     * get actual blind level
     * see remark in header of this file
     */

    function getLevel()
    {
        global $api;
        return (double) $api->getValue($this->peerid, 1, 'LEVEL');
    }

    /*
     * set actual blind level
     * set movement mode 
     * see remark in header of this file
     */
    function setLevel($level, $manual = false)
    {
        if (!$this->getInhibit())
        {
            global $api;
            $count = 0;
            $result = 0;

            $mode = $this->getMode();
            $cur_level = $this->getLevel();
            if ($manual)
            {
                $mode = \Homegear\Blind::MANUAL;
            }

            $this->setMode($mode);
            $this->setLastLevel($cur_level);

            do
            {
                if ($mode == \Homegear\Blind::AUTO)
                {
                    $this->setTimestamp(time() + 40);
                }
                $result = $api->setValue($this->peerid, 1, 'LEVEL', $level);

                if ($count == 0)
                {
                    $this->Log($this->Level2Position($cur_level) . '% > ' . $this->Level2Position($level) . '%');
                }
                else
                {
                    $this->Log($this->Level2Position($cur_level) . '% > ' . $this->Level2Position($level) . '% (' . $count . ')');
                }
            }
            while ($result && ( ++$count < $this->retry));
            if ($this->autorepeat && $result)
            {
                $this->Log($this->Level2Position($cur_level) . '% > ' . $this->Level2Position($level) . '% (set repeat)');
                $now = time();
                $api->addEvent(
                [
                    'TYPE' => \Homegear\Constants\Event::Type_Timed,  
                    'ID' => $this->name.'_REPEAT',    
                    "EVENTTIME" => $now + 60,              
                    'EVENTMETHOD' => 'runScript',    
                    'EVENTMETHODPARAMS' => ['sunrise/setBlindLevel.php', $this->peerid.' '.((double)$level).' 1']
                ]);         
            }
        }
    }

    /*
     * get actual hold time(stamp)
     */

    function getHoldTime()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\Blind::HOLD_TIME, 0);
    }

    /*
     * get actual hold time(stamp)
     */

    function setHoldTime($value)
    {
        global $api;
        $api->setMeta($this->peerid, \Homegear\Blind::HOLD_TIME, $value);
    }

    /*
     * get actual mode (AUTO/MANUAL)
     */

    function getMode()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\Blind::MODE, \Homegear\Blind::MANUAL);
    }

    /*
     * set actual mode (AUTO/MANUAL)
     */

    function setMode($value)
    {
        global $api;
        $api->setMeta($this->peerid, \Homegear\Blind::MODE, $value);
        $this->Log(($value == \Homegear\Blind::MANUAL) ? 'MANUAL' : 'AUTO');
    }

    /*
     * get last level
     */

    function getLastLevel()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\Blind::LAST_LEVEL, 0.0);
    }

    /*
     * set last level
     */

    function setLastLevel($value)
    {
        global $api;
        $api->setMeta($this->peerid, \Homegear\Blind::LAST_LEVEL, $value);
    }

    /*
     * get actual timestamp
     */

    function getTimestamp()
    {
        global $api;
        return $api->getMeta($this->peerid, \Homegear\Blind::TIMESTAMP, 0);
    }

    /*
     * set actual timestamp
     */

    function setTimestamp($value)
    {
        global $api;
        $api->setMeta($this->peerid, \Homegear\Blind::TIMESTAMP, $value);
        $this->Log("timestamp " . strftime('%Y-%m-%d %H:%M:%S', $value));
    }

    /*
     * returns TRUE if blind is moving
     */

    function isWorking()
    {
        global $api;
        return boolval($api->getValue($this->peerid, 1, 'WORKING'));
    }

    /*
     * returns TRUE if blind is moving
     */

    function getInhibit()
    {
        global $api;
        return boolval($api->getValue($this->peerid, 1, 'INHIBIT'));
    }

    /*
     * returns TRUE if blind is moving
     */

    function setInhibit($value)
    {
        if ($value)
        {
            $this->Log("set INHIBIT to TRUE");
        }
        else
        {
            $this->Log("set INHIBIT to FALSE");
        }

        global $api;
        return $api->setValue($this->peerid, 1, 'INHIBIT', boolval($value));
    }

    /*
     * calculate level from position
     */

    function Position2Level($pos)
    {
        return (double) ((100 - $pos) / 100.0);
    }

    /*
     * calculate position from level
     */

    function Level2Position($level)
    {
        return (int) (100 - (100.0 * (double) $level));
    }

}
