<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
if (!isset($act))
$act = JRequest::getCmd( 'act', 'ttimes' );
if (!isset($task))
	$task	= JRequest::getCmd( 'task', '' );

require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."toolbar.ttfsp.html.php" );
switch ( $task ) {
    case 'edit':
    case	 'editA': {
        	TOOLBAR_ttfsp::_EDIT();
        break;
    }
    case 'new': {
	 TOOLBAR_ttfsp::_EDIT();
        break;
    }
    default: {
        switch($act) {
	case "sspec":
	case "proftime":	
	case "ssect":	
 	case "ttimes":
	case "elems":
    case "tspec": {
		TOOLBAR_ttfsp::ITEMS_tt();
                break;
            }
	case 'config': {
		TOOLBAR_ttfsp::CONFIG_tt();
		break;
	}
 	case 'addtimes': {
		TOOLBAR_ttfsp::ADDTIMES_tt();
		break;
	}
	
	case 'torders': {
		TOOLBAR_ttfsp::ORDERS_tt();
		break;
	}
	
 	case 'addtm': {
		TOOLBAR_ttfsp::ADDTIMES_tm();
		break;
	}
	
           default: {
                TOOLBAR_ttfsp::DEFAULT_tt();
                break;
            }
        }
        break;
    }
}

?>