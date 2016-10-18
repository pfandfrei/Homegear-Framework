<?php	
/*
 * Homegear wrapper V0.1 for homegear 0.6.x
 * (c) Frank Motzkau 2016
 * Constants for Homegear methods to make source code more readable
 */

namespace Homegear\Constants;

// constants for Homegear::getPeerId
class GetPeerId
{
	const Filter_Serial		= 1;    // Filter by serial number (e. g. JEQ0478327)
	const Filter_Adress 	= 2;    // Filter by physical address in decimal or hexadecimal format (e. g. 0x47a2b6 or 12748275)
	const Filter_DeviceId 	= 3;	// Filter by device type id	(e. g. 0x8b or 59)
	const Filter_DeviceType = 4;	// Filter by device type string	(e. g. HM-CC-RT-DN)
	const Filter_DeviceName = 5;	// Filter by device name (e. g. 1st Floor Light). Partial values are allowed.
	const Filter_Pending 	= 6;	// All peers with pending config	
	const Filter_Unreach 	= 7;	// All unreachable peers	
	const Filter_CanReach 	= 8;	// All reachable peers	
	const Filter_LowBat 	= 9;	// All peers with low battery	
}

// constants for Homegear::addevent
class Event
{
    const Type_Triggered= 0;
    const Type_Timed    = 1;
    
    const Trigger_Unchanged             =	1;	// The parameter is unchanged compared to the previous value.
    const Trigger_Changed               =	2;	// The parameter has changed compared to the previous value.
    const Trigger_Greater               =	3;	// The parameter is now greater than the previous value.
    const Trigger_Less                  =	4;	// The parameter is now smaller than the previous value.
    const Trigger_GreaterOrUnchanged    =	5;	// The parameter is now greater than the previous value or unchanged.
    const Trigger_LessOrUnchanged       =	6;	// The parameter is now smaller than the previous value or unchanged.
    const Trigger_Updated               =	7;	// The parameter was updated. This trigger is always true when a packet with the specified parameter is received. You have to use this trigger for parameters of type "Action".
    const Trigger_Value                 =	8;	// The parameter value equals "TRIGGERVALUE".
    const Trigger_NotValue              =	9;	// The parameter does not equal "TRIGGERVALUE".
    const Trigger_GreaterThanValue      =	10;	// The parameter is greater than "TRIGGERVALUE".
    const Trigger_LessThanValue         =	11;	// The parameter is smaller than "TRIGGERVALUE".
    const Trigger_GreaterOrEqualValue   =	12;	// The parameter is greater than or equal "TRIGGERVALUE".
    const Trigger_LessOrEqualValue      =	13;	// The parameter is less than or equal "TRIGGERVALUE"
}