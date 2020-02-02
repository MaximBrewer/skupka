<?php
/**
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
**/

defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldCouponStyle extends JFormField {

	public $type = 'CouponStyle';

	protected function getInput() {
		$data = null;
		foreach ((Array)$this->form as $key => $val) {
			if($val instanceof JRegistry){
				$data =$val;
				break;
			}
		}
		
		$data = $data->toArray();
		
		$coupon_style = $data['params']['coupon_style'];
		$coupon_prefix = $data['params']['coupon_prefix'];
		$coupon_suffix =$data['params']['coupon_suffix'];
		
		$coupon = "COUPON_EXAMPLE";
		$html = $coupon_prefix."<div style=\"".$coupon_style."\">".$coupon."</div>".$coupon_suffix;
		return "<div style=\"border:1px solid lightgrey;padding:10px; float: left; margin: 5px 5px 5px 0; width: auto;\">".$html."</div>";
	}
}

?>
