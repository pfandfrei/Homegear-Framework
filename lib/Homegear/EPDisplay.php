<?php
/*
 * Homegear wrapper V0.1 for homegear 0.6.x 
 * (c) Frank Motzkau 2016
 * the EPDisplay class for Homematic HM-Dis-EP-WM55
 * NOTE: not all methods are supported by now
 * 
 * this class will probably work with HM-Dis-WM55 with some small modifications
 */

namespace Homegear;

include_once __DIR__ . '/Device.base.php';

class EPDisplay extends Device
{    
    const TYPE_STRING = 'HM-Dis-EP-WM55';
    
    // number of usable text lines
    const LINES_COUNT   = 3;
    
    // commands
    const CMD_START     = 0x02;
    const CMD_END       = 0x03;
    const TEXT_START    = 0x12;
    const TEXT_END      = 0x11;
    const NEW_LINE      = 0x0a;
    const ICON_NEXT     = 0x13;
    const BEEP_NEXT     = 0x14;
    const SIGNAL_NEXT   = 0x16;
    const REPEAT_NEXT   = 0x1c;
    const DELAY_NEXT    = 0x1d;
    
    // symbols
    const NO_ICON       = 0x00;
    const ICON_OFF      = 0x80;
    const ICON_ON       = 0x81;
    const ICON_OPEN     = 0x82;
    const ICON_CLOSED   = 0x83;
    const ICON_ERROR    = 0x84;
    const ICON_OK       = 0x85;
    const ICON_INFO     = 0x86;
    const ICON_MESSAGE  = 0x87;
    const ICON_SERVICE  = 0x88;

    // text block
    const TXT_BLOCK1    = 0x80;
    const TXT_BLOCK2    = 0x81;
    const TXT_BLOCK3    = 0x82;
    const TXT_BLOCK4    = 0x83;
    const TXT_BLOCK5    = 0x84;
    const TXT_BLOCK6    = 0x85;
    const TXT_BLOCK7    = 0x86;
    const TXT_BLOCK8    = 0x87;
    const TXT_BLOCK9    = 0x88;
    const TXT_BLOCK10   = 0x89;

    // beep code
    const BEEP_OFF  = 0xC0;
    const BEEP_LL   = 0xC1;
    const BEEP_LS   = 0xC2;
    const BEEP_LSS  = 0xC3;
    const BEEP_S    = 0xC4;
    const BEEP_SS   = 0xC5;
    const BEEP_L    = 0xC6;

    // signals
    const FLASH_OFF     = 0xF0;
    const FLASH_RED     = 0xF1;
    const FLASH_GREEN   = 0xF2;
    const FLASH_ORANGE  = 0xF3;

    private $_lines;
    private $_icons;
    private $_beep;
    private $_repeat;
    private $_delay;
    private $_command;
    
    /*
     * creates devices and requests name and friendly name
     * in: peer id or peer name
     */
    public function __construct($peerid)
    {
        parent::__construct($peerid);
        // todo: change number of usable text lines depending on display type (oled e-paper)
        $this->reset();
    }
    
    /* 
     * resets all output information
     */
    public function reset()
    {    
        $this->_lines = ['', '', ''];
        $this->_icons = [\Homegear\EPDisplay::NO_ICON, \Homegear\EPDisplay::NO_ICON, \Homegear\EPDisplay::NO_ICON];
        $this->_beep = \Homegear\EPDisplay::BEEP_OFF;
        $this->_delay = 0;
        $this->_repeat = 0;
        $this->_signal = \Homegear\EPDisplay::FLASH_OFF;
    }
    
    /* 
     * set text and icon for one specific line
     */
    public function setLine($index, $text, $icon=\Homegear\EPDisplay::NO_ICON)
    {    
/*
 * ; - Sanduhr
 * < - Pfeil S
 * = - Pfeil N
 * > - Pfeil NO
 * @ - Pfeil SO
 * ^ - Grad
 */
        if ($index>=1 && $index<= \Homegear\EPDisplay::LINES_COUNT)
        {
            $this->_lines[$index-1] = substr($text, 0, 12);
            $this->_lines[$index-1] = str_replace('Ä', '[', $this->_lines[$index-1]);
            $this->_lines[$index-1] = str_replace('Ö', '#', $this->_lines[$index-1]);
            $this->_lines[$index-1] = str_replace('Ü', '$', $this->_lines[$index-1]);
            $this->_lines[$index-1] = str_replace('ä', '{', $this->_lines[$index-1]);
            $this->_lines[$index-1] = str_replace('ö', '|', $this->_lines[$index-1]);
            $this->_lines[$index-1] = str_replace('ü', '}', $this->_lines[$index-1]);
            $this->_icons[$index-1] = $icon;
        }
    }
    
    /* 
     * set beep code
     */
    public function setBeep($beep, $repeat, $delay)
    {    
        if ($repeat<=1)
        {
            $this->_repeat = 0xdf; 
        }
        else if ($repeat>14)
        {
            $this->_repeat = 0xde; 
        }
        else
        {
            $this->_repeat = 0xd0 + $repeat - 1;
        }
        
        if ($delay > 160) 
        {
            $delay = 160;
        }
        $this->_beep    = $beep;
        $this->_delay   = 0xe0 + (int)(($delay - 1)/10);
    }
    
    /* 
     * set led flash signal
     */
    public function setSignal($signal)
    {    
        $this->_signal   = $signal;
    }
    
    /* 
     * send all data to display
     */
    function display()
    {
        $this->_command = '';
        $this->encode(EPDisplay::CMD_START);
        //$this->encode(EPDisplay::NEW_LINE);
        for ($i=0; $i<\Homegear\EPDisplay::LINES_COUNT; $i++)
        {
            $this->encode(EPDisplay::NEW_LINE);
            if (strlen($this->_lines[$i])>0)
            {
                $this->encode(EPDisplay::TEXT_START);
                $this->encodeText((string)$this->_lines[$i]);
            }
            if ($this->_icons[$i] != EPDisplay::NO_ICON)
            {
                $this->encode(EPDisplay::ICON_NEXT); 
                $this->encode($this->_icons[$i]); 
            }
        }
        $this->encode(EPDisplay::BEEP_NEXT);
        $this->encode($this->_beep); 
        $this->encode(EPDisplay::REPEAT_NEXT);
        if ($repeat<=1)
        {
            $this->encode(0xdf); 
        }
        else if ($repeat>14)
        {
            $this->encode(0xde); 
        }
        else
        {
            $this->encode(0xd0 + $repeat - 1);
        }
        $this->encode(EPDisplay::DELAY_NEXT);
        $this->encode($this->_delay);
        $this->encode(EPDisplay::SIGNAL_NEXT);
        $this->encode($this->_signal);
        $this->encode(EPDisplay::CMD_END);
        
        global $api;
        $api->setValue($this->peerid, 3, 'SUBMIT', $this->_command);
    }
    
    /* 
     * encode single data 
     */
    private function encode($data)
    {   
        if ($data < 16)
        {
            $this->_command .= '0';
        }
        $this->_command .= dechex($data);
    }
    
    /* 
     * encode text line
     */
    private function encodeText($data)
    {
        for ($i=0; $i<strlen($data); $i++)
        {
            $this->_command .= dechex(ord($data[$i]));
        }
    }
}
