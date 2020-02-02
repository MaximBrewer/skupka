<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */


// no direct access
defined('_JEXEC') or die;

class JModelFilters extends OPCModel {
	public function store() {
		
		$catfilter1 = JRequest::getVar('catfilter1'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		
		if (!empty($catfilter1))
		foreach ($catfilter1 as $sid=>$v) {
			OPCconfig::clearConfig('opcfilters', 'catfilter1', $sid); 
			$ins = $this->toArray($v); 
			if (!empty($ins)) {
				OPCconfig::store('opcfilters', 'catfilter1', $sid, $ins); 
			}
		}
		
		$catfilterp = JRequest::getVar('catfilterp'); 
		if (!empty($catfilterp))
		foreach ($catfilterp as $sid=>$v) {
			OPCconfig::clearConfig('opcfilters', 'catfilterp', $sid); 
			$ins = $this->toArray($v); 
			if (!empty($ins)) {
				OPCconfig::store('opcfilters', 'catfilterp', $sid, $ins); 
			}
		}
		
		$catfilterPS1 = JRequest::getVar('catfilterPS1'); 
		if (!empty($catfilterPS1))
		foreach ($catfilterPS1 as $sid=>$v) {
			OPCconfig::clearConfig('opcfilters', 'catfilterPS1', $sid); 
			$ins = $this->toArray($v); 
			if (!empty($ins)) {
				OPCconfig::store('opcfilters', 'catfilterPS1', $sid, $ins); 
			}
		}
		
		$catfilterP1 = JRequest::getVar('catfilterP1'); 
		if (!empty($catfilterP1))
		foreach ($catfilterP1 as $sid=>$v) {
			OPCconfig::clearConfig('opcfilters', 'catfilterP1', $sid); 
			$ins = $this->toArray($v); 
			if (!empty($ins)) {
				OPCconfig::store('opcfilters', 'catfilterP1', $sid, $ins); 
			}
		}
		
		
		
		
	}
	
	public function getConfigTxt($key='catfilter1', $sid) {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		$default = array(); 
		$ret = OPCconfig::getValue('opcfilters', $key, $sid, $default, false, false); 
		if (empty($ret)) return ''; 
		if (is_object($ret)) $ret = (array)$ret; 
		$r = implode(',', $ret); 
		return $r; 
		
	}
	
	private function toArray($v) {
		    $na = array(); 
		    $va = explode(',', $v); 
			foreach ($va as $vv) {
				$vv = trim($vv); 
				$vv = (int)$vv; 
				if (!empty($vv)) $na[$vv] = $vv; 
				
			}
			return $na; 
	}
}