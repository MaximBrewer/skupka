<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
defined('_JEXEC') or die('Restricted access');
	 
	 //$dispatcher = JDispatcher::getInstance(); 
	 //$shipmentid: 
	 // 	 -> is a from <input type="radio" id="myid1" ...
	 // 	 -> is a from <input type="radio" id="myid2" ...
	 // OR
	 // when using select inside shipping and it is marked with
	 // <option ismulti="true" multi_id="shipment_id_'.$method->virtuemart_shipmentmethod_id.'_'.$ppp->id.'" 
	 // full multi_id is sent here
	 // to parse additional details such as json, you can use: 
	 // $dataa = OPCTransform::getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data*');
	 // where data-json=\''.json_encode... within your html as defined: 
	 // function getFT($html, $tagname, $mustIncl='', $mustProp='', $mustVal='', $ending='>', $getProp)
	 
	 // cart -> virtuemart cart with currently calculated shipping AND payment (will itenerate all payments as well timex number of multi shipping)
	 // shipping_method -> is the html of the current shipping method including all of the multi shipments -> may get transofrmed into options by opc if set up
	 // id -> virtuemart shipment ID
	 // $html -> it is the output html that will always be rendred inside the main checkout form
	 // all data sent with data-json="<?php json_encode(array('uri_key'=>uri_value))" will automatically be available via JRequest::getVar('uri_key')
	 $extra_json = JRequest::getVar('extra_json'); 
	 if (!empty($extra_json))
	 {
		 $dataJ = @json_decode($extra_json, true); 
		 if (!empty($dataJ))
		 {
			 foreach ($dataJ as $k=>$v)
			 {
			   $x = JRequest::getVar($k); 
			   if (empty($x))
			   {
				   JRequest::setVar($k, $v); 
				  
			   }
			 }
		 }
	 }
	 
	 
	 $results = $dispatcher->trigger('setOPCbeforeSelect', array( $cart, $shipmentid, $shipping_method, $id, &$html )); 
	 
	 // your plugin function should use JRequest::set
	 // which you probably will get when calling 
	 // plgVmOnSelectCheckShipment
	 // or by calculation, etc... 
	 
	 