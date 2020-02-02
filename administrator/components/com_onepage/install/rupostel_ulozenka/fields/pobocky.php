<?php
defined ('_JEXEC') or die();
jimport('joomla.form.formfield');
class JFormFieldPobocky extends JFormField {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'pobocky';

	function getInput () {
		
		//$name, $value, &$node, $control_name
		
		$name = $this->name; 
		$value = $this->value; 
		$control_name = 'params'; 
		
		$db = JFactory::getDBO(); 
		$cid = JRequest::getVar('cid'); 
		$q = 'select shipment_params from #__virtuemart_shipmentmethods where shipment_element = \'rupostel_ulozenka\' '; 
		if (!empty($cid))
		 {
		   $cid = (int)$cid[0]; 
		   
		   $q .= ' and virtuemart_shipmentmethod_id = '.$cid; 
		 }
		$db->setQuery($q); 
		
		$params = $db->loadResult(); 
		
		$obj = new stdClass(); 
		
		$e = $db->getErrorMsg(); 
		if (!empty($e)) {
		  JFactory::getApplication()->enqueueMessage($e); 
		  return; 
		}
		$err = true; 
		if (empty($params)) $err = true; 
		else
		{
		$a = explode('|', $params); 
		
		foreach ($a as $p)
		 {
		    $a2 = explode('=', $p); 
			if (!empty($a2) && (count($a2)==2))
			 {
			   $keyX = $a2[0]; 
			   $obj->$keyX = json_decode($a2[1]); 
			 }
		 }
		 
		 
		 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'rupostel_ulozenka'.DIRECTORY_SEPARATOR.'helper.php'); 
		
		
		$xml = UlozenkaHelper::getPobocky($obj, false); 
		
		
		
		$html = ''; 
		if (!empty($xml->error)) return $xml->error; 
		
		if (!empty($xml))
		 {
		 if (isset($xml->body)) {
					$html = '
					<div class="row">
					   
						<div colspan="4">
							<center><span style="color:red;font-weight:bold;">'.$xml->body->div.'</span></center>
						</div>
					</dir>
					
					
					'; 
					
				}
				
			if (empty($html))
			{
				$html .= ' <div><select name="enable_all" onchange="return mySubmit(this);""><option value="0">-</option><option value="1">Povoliť všetky</option><option value="2">Vypnúť všetky</option></select></div>
				<script>
					function mySubmit(el)
					{
						if (typeof jQuery != \'undefined\') {
						  var p = jQuery(\'.pobockaenabled\');
						  if (p.length > 0) { 
						   p.each(function() { 
						    var e = jQuery(this); 
							if (el.options[el.selectedIndex].value == 1)
							{
							e.attr(\'checked\', \'checked\'); 
							}
							else
							if (el.options[el.selectedIndex].value == 2)
							{
							e.removeAttr(\'checked\');
							}
								
						   }); 
						  }
						 
						}
						return; 
						Joomla.submitbutton(\'apply\');
					}
					</script>
				
				'; 
			}
				
		   $k=1;
		   foreach ($xml->pobocky as $p)
		     {
				 
			   if (empty($obj->pobocky)) $obj->pobocky = new stdClass(); 
				 
			   $err = false; 
			   $enabled_const = 'enabled'.$p->id; 
			   $parcel_price = 'parcelprice'.$p->id; 
			   $dobierka_price = 'codprice'.$p->id; 
			   
			   if (empty($obj->pobocky->$parcel_price))
			   {
				 if (isset($obj->pobocky))
			     $obj->pobocky->$parcel_price = $p->prices->parcel; 
			   }
			   if (empty($obj->pobocky->$dobierka_price))
			   {
				if (isset($obj->pobocky))
			    $obj->pobocky->$dobierka_price = $p->prices->cashOnDelivery; 
			   }
			   

			   

							$price_const = 'ULOZENKA_'.strtoupper((string)$p->zkratka).'_PRICE';
							if ((!empty($obj->pobocky->$enabled_const))) {
								
								
								
									$enabled = "checked=\"checked\""; 
								} else {
									$enabled = ""; 
								}
							
							
							
							$price = defined($price_const)?constant($price_const):'0';
							$html .= "<div class=\"row row$k\"><div style=\"border-bottom: 1px solid #ddd;\" colspan=\"3\" width=\"25%\"><b style=\"margin-top: 15px;\">".$p->nazev; 
							if (!empty($p->partner)) {
							$html .= ' (Partner) '; 
							}
							$html .= "</b>"
								.'</div><div class="provoz" style="float: right;" width="25%" rowspan="2">'
								.$p->provoz
								.'</div><div width="25%" rowspan="2">'; 
								
								if (empty($p->aktiv)) { $enabled = ' disabled="disabled" '; }
								if (isset($obj->partners))
								if (empty($obj->partners) && (!empty($p->partner)))
								{
								
								 $enabled = ' disabled="disabled" '; 
								}
								
								
								
								
								
								
								$html .= '<label><b>Povolit: </b><input class="inputbox pobockaenabled" type="checkbox" name="'.$name.'['.$enabled_const.']" '.$enabled.' value="1" /></label>'
								.'</div><div width="30%"  rowspan="2">'
								.'Cena za dopravu (parcel) ('.$p->prices->parcel.' '.$p->prices->currency.'): '
								.'<input class="inputbox" type="text" name="'.$name.'['.$parcel_price.']"  value="'.$obj->pobocky->$parcel_price.'" /><br />'
								.'Priplatek za dopravu (dobirka) ('.$p->prices->cashOnDelivery.' '.$p->prices->currency.'): '
								.'<input class="inputbox" type="text" name="'.$name.'['.$dobierka_price.']"  value="'.$obj->pobocky->$dobierka_price.'" /><br />'
								."</div></div>\n";
							$html .= "<div class=\"row row$k\"><div colspan=\3\" width=\"30%\">"
								.$p->ulice."<br />\n".$p->obec."<br />\n".$p->psc
								."</div></div>\n";
							$pobocky_zkratky[]=strtoupper((string)$p->zkratka);
							$k=abs($k-1);
			 }
		 }
		if (!$err)
		if (!empty($html)) return $html; 
		//return '<input type="text" name="params[' . $name . ']" id="params' . $name . '" value="' . $value . '" class="text_area" size="50">';
		}
		if ($err)
		 {
		    return 'Nastavte kluc, a ID obchodu kliknite ulozit a nasledne sa zobrazia pobocky pre ktore je mozne nastavit cenu.';
		 }
	}

}