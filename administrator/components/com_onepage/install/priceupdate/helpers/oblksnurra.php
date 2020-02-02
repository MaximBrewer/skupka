<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: ps_product.php 2615 2010-10-31 12:07:48Z zanardi $
* @package VirtueMart
* @subpackage classes
* @copyright Copyright (C) 2004-2009 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

class ArtiObj {
    public $_AFP1;
    public $_AFP2;
    public $_AFP3;
    public $_AFP4;
    public $_AFP5;
    public $_AFP1M;
    public $_AFP2M;
    public $_AFP3M;
    public $_AFP4M;
    public $_AFP5M;
    public $_AQUANTITY;
    public $_ATYP;
    public $_AMOMS;
    public $_AAGRP;
    public $_VariantBas;
    public $_artnr;
    public $_error;
    
   function __construct($artnr) 
   {
        $db = JFactory::getDBO();
        $this->_artnr = $artnr;
        $this->_VariantBas = "";
        $q = "SELECT AFP1, AFP2, AFP3, AFP4, AFP5, AFP1M, AFP2M, AFP3M, AFP4M, AFP5M, AQUANTITY, ATYP, AMOMS, AAGRP from ARTI2 where ARTNR='" . $artnr . "' limit 0,1";
        $db->setQuery($q);
		$list = $db->loadAssoc(); 

        if (!empty($list)) {
           $this->_AFP1 = (float)$list['AFP1'];
           $this->_AFP2 = (float)$list['AFP2'];
           $this->_AFP3 = (float)$list['AFP3'];
           $this->_AFP4 = (float)$list['AFP4'];
           $this->_AFP5 = (float)$list['AFP5'];
           $this->_AFP1M = $list['AFP1M'];
           $this->_AFP2M = $list['AFP2M'];
           $this->_AFP3M = $list['AFP3M'];
           $this->_AFP4M = $list['AFP4M'];
           $this->_AFP5M = $list['AFP5M'];            
           
           $this->_AQUANTITY = $list['AQUANTITY'];            
           $this->_ATYP = $list['ATYP'];            
           $this->_AMOMS = $list['AMOMS'];            
           $this->_AAGRP = $list['AAGRP'];            
           
           $this->_error = "OK";
        } else {
           $this->_error = "Hittar inte ARTI med artnr=".$artnr;
        }
   }
}

class OblkObj {
   public $_error;
   public $_OBRAB;
   public $_OBPR;
   public $_OBLNR;

   function __construct($obknd, $obtyp, $obart, $obval) 
   {
        $db = JFactory::getDBO();
        $q = "SELECT OBRAB, OBPR, OBLNR from OBLK where OBKND='".$obknd."' and OBTYP='".$obtyp."' and OBART='".$obart."' and OBVAL='".$obval."' limit 0,1";

        $db->setQuery($q);
		$list = $db->loadAssoc(); 
		
        if (!empty($list)) {
           $this->_OBRAB = $list['OBRAB'];
           $this->_OBPR = $list['OBPR'];
           $this->_OBLNR = $list['OBLNR'];
           $this->_error = "OK";
        } else {
           $this->_error = "Hittar inte OBLK med obknd=".$obknd." obtyp=".$obtyp." obart=".$obart." obval=".$obval;
        }
   }
}

class KundObj {
    public $_error;
    public $_KPRIS;
    public $_KRABS;
    public $_KMOMS;
    public $_KKAT;
    public $_KNR;
    
   function __construct($kundnr) 
   {
        $db = JFactory::getDBO();
        $q = "SELECT KPRIS, KRABS, KMOMS, KKAT, KNR from KUND where KNR='".$kundnr."' limit 0,1";

        $db->setQuery($q);
		$list = $db->loadAssoc(); 
		
        if (!empty($list)) {
           $this->_KPRIS = $list['KPRIS'];
           $this->_KRABS = $list['KRABS'];
           $this->_KMOMS = $list['KMOMS'];
           $this->_KKAT = $list['KKAT'];
           $this->_KNR = $list['KNR'];
           $this->_error = "OK";
        } else {
           $this->_error = "Customer not found ".(int)$kundnr;
		 }
   }
}

class QuantityObj {
    public $_error;
    public $_QT_CODE;
    public $_QT_DESC;
    public $_QT_TYPE;
    public $_QT_PRICE2;
    public $_QT_PRICE3;
    public $_QT_PRICE4;
    public $_QT_PRICE5;
    public $_QT_RAB2;
    public $_QT_RAB3;
    public $_QT_RAB4;
    public $_QT_RAB5;

    function __construct($quantity_idx_1) {
        $db = JFactory::getDBO();
        $q = "SELECT QT_CODE, QT_DESC, QT_TYPE, QT_PRICE2, QT_PRICE3, QT_PRICE4, QT_PRICE5, QT_RAB2, QT_RAB3, QT_RAB4, QT_RAB5 from QUANTITY where QT_CODE='".$quantity_idx_1."' limit 0,1";

        $db->setQuery($q);
		$list = $db->loadAssoc(); 
        if (!empty($list)) {
           $this->_QT_CODE = $list['QT_CODE'];
           $this->_QT_DESC = $list['QT_DESC'];
           $this->_QT_TYPE = $list['QT_TYPE'];
           $this->_QT_PRICE2 = $list['QT_PRICE2'];
           $this->_QT_PRICE3 = $list['QT_PRICE3'];
           $this->_QT_PRICE4 = $list['QT_PRICE4'];
           $this->_QT_PRICE5 = $list['QT_PRICE5'];
           $this->_QT_RAB2 = $list['QT_RAB2'];
           $this->_QT_RAB3 = $list['QT_RAB3'];
           $this->_QT_RAB4 = $list['QT_RAB4'];
           $this->_QT_RAB5 = $list['QT_RAB5'];
           $this->_error = "OK";
        } else {
           $this->_error = "Hittar inte QUANTITY med quantity_idx_1=".$quantity_idx_1;
        }
    }
}

class Rabatt {
    var $_aprisOK;
    var $_oblk_discount;
    var $_F_ONET;
    var $_F_RAB;
    var $_oblk_price_list_no;
    var $_F_APRIS;
    var $_F_OPMOMS;
    var $_message_str;
    
    var $_artnr_str;
    var $_customerObj;
    var $_productObj;
    var $_afpxArr;
    var $_afpxmArr;
    var $_lpl;
    var $_aokva;
    var $_currPris;
    var $_oblk;
    var $_bOblkSearched;

    function __construct($kundnr, $theArtnr, $lpl, $aokva, $currPris) {
        $this->_customerObj = new KundObj($kundnr);
        $this->_artnr_str = $theArtnr;
        $this->_lpl = $lpl;
        $this->_aokva = $aokva;
        $this->_currPris = $currPris;

        $this->_aprisOK = false;
        $this->_oblk_discount = -1;
        $this->_F_ONET = "";
        $this->_F_RAB = -1;
        $this->_oblk_price_list_no = 0;
        $this->_F_APRIS = -1;
        $this->_F_OPMOMS = -1;
        $this->_message_str = "";
        $this->_bOblkSearched = false;
    }
    
    function oblkSnurra () {
        if ($this->_customerObj->_error != "OK") {
            return;
        }
        
        $this->_productObj = new ArtiObj($this->_artnr_str);
        
        if ($this->_productObj->_error != "OK") {
            return;
        }

        $this->_afpxArr = array(0 => $this->_productObj->_AFP1, 1 => $this->_productObj->_AFP2, 2 => $this->_productObj->_AFP3, 3 => $this->_productObj->_AFP4, 4 => $this->_productObj->_AFP5);
        $this->_afpxmArr = array(0 => $this->_productObj->_AFP1M, 1 => $this->_productObj->_AFP2M, 2 => $this->_productObj->_AFP3M, 3 => $this->_productObj->_AFP4M, 4 => $this->_productObj->_AFP5M);

        if ($this->_customerObj->_error == "OK") {
            $oblk = $this->GetOblk ();

            if ($oblk->_error == "OK") {

                $this->_aprisOK = true;
                $this->_oblk_discount = $oblk->_OBRAB;
                $this->_F_ONET = "B";
                $obpr = $oblk->_OBPR;
                $this->_F_RAB = $oblk->_OBRAB;
                
                if ($obpr != 0) {

                    $this->_oblk_price_list_no = 0;
                    $this->_F_APRIS = $obpr;
                    $plnr = $oblk->_OBLNR;
                    $moms = $this->_afpxmArr[$plnr - 1];
                    $this->_F_OPMOMS = $moms;
                } else {
                    $oblnrStr = $oblk->_OBLNR;

                    if ($oblnrStr == "0" || $oblnrStr == " ") {
	                    // -----------------------------------
	                    // this is an impossible case for OBLK
	                    // registrated in the new system.
	                    // This case is for old customers
	                    // with converted data.
	                    // -----------------------------------
	                    $this->_oblk_price_list_no = $this->_customerObj->_KPRIS;
                            $mess_str = "";
	                    $price_list_no = 0;
	                    $rabatt = 0;
	                    $status = $this->check_QUANTITY ($mess_str, $this->_aokva, 
			                                         $price_list_no, 
			                                         $rabatt);
                        if ($mess_str != "") {
                            if ($this->_message_str != "") {
                                $this->_message_str += ", ";
                            }

                            $this->_message_str += mess_str;
                        }

                        if ($status == 0) {
                            $this->put_price ($price_list_no);
                            $this->_F_RAB = $rabatt;
	                    } else {
                            $this->put_price ($this->_customerObj->_KPRIS);
	                    }
                    } else {
	                    $this->_oblk_price_list_no = $oblnrStr;

                            $mess_str;
	                    $price_list_no;
	                    $rabatt;
	                    $status = $this->check_QUANTITY ($mess_str, 
                                                     $this->_aokva,
			                                         $price_list_no,
			                                         $rabatt);

                        if ($mess_str != "") {
                            if ($this->_message_str != "") {
                                $this->_message_str += ", ";
                            }

                            $this->_message_str .= $mess_str;
                        }

                        if ($status == 0) {
	                        $this->put_price ($price_list_no);
                            $this->_F_RAB = $rabatt;
	                    } else {
                            $this->put_price ($oblnrStr);
	                    }
                    }
                }
            }
        }

        if ($this->_aprisOK == false) {
            $mess_str;
	    $price_list_no;
	    $rabatt;
	    $status = $this->check_QUANTITY ($mess_str, 
                                       $this->_aokva,
		                               $price_list_no,
		                               $rabatt);

            if ($mess_str != "") {
                if ($this->_message_str != "") {
                    $this->_message_str += ", ";
                }

                $this->_message_str .= mess_str;
            }

            if ($status == 0) {
                $this->put_price ($price_list_no);
                $this->_F_RAB = $rabatt;
	        } else if ($this->_customerObj != null) {
                $this->put_price($this->_customerObj->_KPRIS);

                if ($this->_customerObj->_KRABS > 0) {
		           $this->_F_ONET = "K";
		            $this->_F_RAB = $this->_customerObj->_KRABS;
		        } else {
		            $this->_F_ONET = " ";
		            $this->_F_RAB = 0.0;
		        }
	        }
	    }
    }

    function check_QUANTITY (&$message_str, $okvan, &$price_list_no, &$rabatt) {
        $message_str = "";
        $price_list_no = 0;
        $rabatt = 0;
	$rk_str = $this->_F_ONET;

        if ($rk_str != "")
        {
            switch (substr($rk_str, 0, 1))
            {
                case "P":
                    // ------------------------------
                    // the user has changed the price
                    // ------------------------------
                    return -1;
                case "B":
                    if ($this->_oblk_price_list_no == 0)
                    {
                        // ---------------------------------------
                        // the user has block with a fix price set
                        // ---------------------------------------
                        return -1;
                    }
                    break;
                default:
                    break;
            }
        }
        // ---------------------------
        // no quantity on this article
        // ---------------------------
        $quantity_idx_1 = $this->_productObj->_AQUANTITY;

        if (trim($quantity_idx_1) == "") {
            return -1;
        }

        $quantity = new QuantityObj(quantity_idx_1);
        
        if ($quantity->_error != "OK") {
            return -1;
        }

        $tmp_price_list_no = 0;
        $quantArr = array(0 => $quantity->_QT_PRICE2, 1 => $quantity->_QT_PRICE3, 2 => $quantity->_QT_PRICE4, 3 => $quantity->_QT_PRICE5);
        $quantRabArr = array(0 => $quantity->_QT_RAB2, 1 => $quantity->_QT_RAB3, 2 => $quantity->_QT_RAB4, 3 => $quantity->_QT_RAB5);

        for ($qt_priceX = 0; $qt_priceX <= 3; $qt_priceX++) {
	        $quant = $quantArr[$qt_priceX];

	        if ($quant == 0 || ($okvan < $quant)) {
	            // ---------------------
	            // choose prev pricelist
	            // ---------------------
	            $tmp_price_list_no = $qt_priceX + 1;
	            break;
	        } else if ($qt_priceX == 3) {
	            // ------------------------
	            // choose current pricelist
	            // ------------------------
	            $tmp_price_list_no = $qt_priceX + 2;	// "5"
	            break;
	        }
        }

        // ----------------------------------------------
        // look up  OBLK.OBRAB and save in _oblk_discount
        // ----------------------------------------------
        if ($this->_customerObj->_error == "OK" && ($rk_str == "B" || $this->_oblk_discount < 0.0)) {

	        if ($this->_productObj->_ATYP != 3) {	// ARTI.ATYP != "3"
                $oblk = $this->GetOblk();
            
                if ($oblk != null) {
	                $this->_oblk_discount = $oblk->_OBRAB;
	            }
	        }
        }

        if ($quantity->_QT_TYPE == "P") {
	        if ($rk_str == "B") {
	            $price_list_no = max ($tmp_price_list_no, $this->_oblk_price_list_no);

		        if ($price_list_no != $this->_oblk_price_list_no) {
			        $this->_F_ONET = "Q";
		        }

	            $rabatt = $this->_oblk_discount;
	        } else {
		        if ($this->_oblk_price_list_no == 0) {
                            $price_list_no = max ($tmp_price_list_no, ($this->_customerObj->_error != "OK") ? 0 : $this->_customerObj->_KPRIS);

		            if ($price_list_no != (($this->_customerObj->_error != "OK") ? 0 : $this->_customerObj->_KPRIS)) {
			            $this->_F_ONET = "Q";
		            } else {
			            if ($this->_customerObj->_error == "OK" && $this->_customerObj->_KRABS > 0) {
				            $this->_F_ONET = "K";
			            } else {
				            $this->_F_ONET = " ";
			            }
		            }

                    $rabatt = ($this->_customerObj->_error != "OK") ? 0 : $this->_customerObj->_KRABS;
                } else {
		        $this->_F_ONET = "B";
	                $price_list_no = max ($tmp_price_list_no, $this->_oblk_price_list_no);

		            if ($price_list_no != $this->_oblk_price_list_no) {
			            $this->_F_ONET = "Q";
		            }

		            $rabatt = $this->_oblk_discount;
		        }
	        }
        } else {	// --- RABATT
	        $qt_rab = 0.0;

	        if ($tmp_price_list_no != 1) {
	            $qt_rab = $quantRabArr[$tmp_price_list_no - 2];
	        }

	        if ($rk_str == "B") {
	            $price_list_no = $this->_oblk_price_list_no;
	            $rabatt = max ($qt_rab, $this->_oblk_discount);

		        if ($rabatt != $this->_oblk_discount) {
        			$this->_F_ONET = "Q";
		        }
	        } else {
		        if (_oblk_price_list_no == 0) {
                    $price_list_no = ($this->_customerObj->_error != "OK") ? 0 : $this->_customerObj->_KPRIS;
                    $myKundRab = ($this->_customerObj->_error != "OK") ? 0 : $this->_customerObj->_KRABS;
                    $rabatt = max($qt_rab, $myKundRab);

                    if ($rabatt != $myKundRab)
                    {
			            $this->_F_ONET = "Q";
		            } else {
			            if ($myKundRab > 0) {
				            $this->_F_ONET = "K";
			            } else {
				            $this->_F_ONET = " ";
			            }
		            }
		        } else {
		            $this->_F_ONET = "B";
	                $price_list_no = $this->_oblk_price_list_no;
	                $rabatt = max (qt_rab, $this->_oblk_discount);

		            if ($rabatt != $this->_oblk_discount) {
            			$this->_F_ONET = "Q";
		            }
		        }
	        }
        }

	    $this->get_QUANTITY_mess ($message_str, $quantity, $tmp_price_list_no, $quantArr);

        return 0;
    }

    function get_QUANTITY_mess (&$message_str, $quantity, $price_list_no, $quantArr) {
        $message_str = "";

	    if ($price_list_no == 5) {
		    return -1;
	    }

        $antal = $quantArr[$price_list_no - 1];

	    if ($antal == 0) {
		    return -1;
	    }

	    $message_str = " Rabatt!! Antal ";
	    $message_str .= antal . " ST = ";

        if ($quantity->_QT_TYPE == "P") {
		    $price = $this->_afpxArr[$price_list_no];
		    $message_str .= "( " . price . " )";
	    } else {
		    $qt_rab = $quantArr[$price_list_no - 1];
                    $message_str .= $qt_rab . "%";
	    }

	    return 0;
    }

    function put_price ($price_list_no) {
	    $apris;
	    $amoms;
	    $this->get_price ($price_list_no, $apris, $amoms);

	    $this->_F_APRIS = $apris;
            $this->_F_OPMOMS = $amoms;

        return 0;
    }

    function get_price ($price_list_no, &$aprice, &$amoms) {
        $omoms_str = ($this->_customerObj->_error != "OK") ? "J" : $this->_customerObj->_KMOMS;
        $afpm = max($price_list_no - 1, 0);
        $amoms = $this->_afpxmArr[$afpm];

        $afp = max($price_list_no - 1, 0);
	    $apris = $this->_afpxArr[$afp];

	    if ($omoms_str == "N" && $amoms == 0) {
	        $radmoms; $this->get_rad_moms ($radmoms);
	        $apris = $apris / ((100 + $radmoms) / 100);
	        $amoms = 1;
	    }
	    $aprice = $apris;

        return 0;
    }

    function isBegagnad()
    {
        return false;//BegManager.IsBegLpl(_lpl);
    }

    function get_rad_moms (&$moms) {

        $omkl_str;
        
        if (isBegagnad ()) {
            $omkl_str = "0";
        } else {
            $omkl_str = $this->_productObj->_AMOMS;
	}

        get_rad_moms2 ($moms, "0", $omkl_str);

        return 0;
    }

    function get_rad_moms2 (&$radmoms, $omsat_str, $ordr_omkl_str) {
        $omkl_str;

        if ($omsat_str == "0") {

	    $omkl_str = $ordr_omkl_str;

        } else {	// --- omsat_str [1-4]

	    $omkl_str = $omsat_str;
        }

        return get_moms ($omkl_str, $radmoms);
    }

    function get_moms ($momsklass_str, &$moms) {
        $moms = 0;
        $ms_code = $momsklass_str;
        
        $db = JFactory::getDBO();

        $q = "SELECT MS_MSATS from MOMS where MS_CODE='" . ms_code . "' limit 0,1";
        $db->setQuery($q);
		$list = $db->loadAssoc(); 
        if (!empty($list)) {
            $moms = $list['MS_MSATS'];
            return 0;
        } else {
            return -1;
        }
    }

    function getMappWeb ($artnr) {
		return ''; 
        $db = JFactory::getDBO();
        $q = "select g.golfid from #__virtuemart_products p, #__virtuemart_product_categories c, #__virtuemart_category_golf g where p.product_sku='".$artnr.
                "' and p.virtuemart_product_id=c.virtuemart_product_id and c.category_id=g.category_id and g.golfid in (select OBART from OBLK where OBTYP in ('M', 'R')) limit 0,1";

        $db->setQuery($q);
		$list = $db->loadAssoc(); 
		
        if (!empty($list)) {
           return $list['golfid'];
        } else {
            return "";
        }
    }
    
    function GetOblk()
    {
        if ($this->_bOblkSearched)
        {
            return $this->_oblk;
        }

        $artg = $this->_productObj->_AAGRP;
        $kkat = $this->_customerObj->_KKAT;
        $knr = $this->_customerObj->_KNR;
        $artnr = $this->_productObj->_VariantBas;
        $kundavtalIteratorStart = 0;

        if ($artnr == "") {
            $artnr = $this->_artnr_str;
        } else if ($artnr != $this->_artnr_str) {
            $kundavtalIteratorStart = -2;
        }

        $mappWeb = "";
        $oblk = new OblkObj("", "", "", "");
        $this->_bOblkSearched = true;

        for ($i = $kundavtalIteratorStart; $oblk->_error != "OK" && $i < 6; $i++)
        {
            switch ($i)
            {
                case -2:
                    //
                    // KUND/ARTIKEL Variant
                    //
                    $oblk = new OblkObj($knr, "N", $this->_artnr_str, "SEK");
                    break;

                case -1:
                    //
                    // KUNDKAT/ARTIKEL Variant
                    //
                    $oblk = new OblkObj($kkat, "P", $this->_artnr_str, "SEK");
                    break;

                case 0:
                    //
                    // KUND/ARTIKEL
                    //
                    $oblk = new OblkObj($knr, "N", $artnr, "SEK");
                    break;

                case 1:
                    //
                    // KUND/MAPP
                    //
                    $mappWeb = $this->getMappWeb ($artnr);
                    
                    if ($mappWeb != "") {
                        $oblk = new OblkObj($knr, "M", $mappWeb, "SEK");
                    }
                    break;

                case 2:
                    //
                    // KUND/ARTIKELGRUPP
                    //
                    $oblk = new OblkObj($knr, "G", $artg, "SEK");
                    break;

                case 3:
                    //
                    // KUNDKAT/ARTIKEL
                    //
                    $oblk = new OblkObj($kkat, "P", $artnr, "SEK");
                    break;

                case 4:
                    //
                    // KUNDKAT/MAPP
                    //
                    if ($mappWeb != "") {
                        $oblk = new OblkObj($kkat, "R", $mappWeb, "SEK");
                    }
                    break;

                case 5:
                    //
                    // KUNDKAT/ARTIKELGRUPP
                    //
                    $oblk = new OblkObj($kkat, "Q", $artg, "SEK");
                    break;

                default:

                    break;
            }
        }

        $this->_oblk = $oblk;
        return $oblk;
    }
}
      
class oblksnurra {
    public static function getPris($product_id, $user_id, $product_price, $sku='') {
        $origPrice = $product_price;
		if (empty($sku)) {
        $db = JFactory::getDBO(); 
        $q = "select product_sku from #__virtuemart_products where virtuemart_product_id=" . (int)$product_id;
        $db->setQuery($q); 
		$sku = $db->loadResult();
		}
		
		
        if ($sku) {
            $artnr = $sku;
            $knr = 'W' . $user_id;
            $rabattObj = new Rabatt($knr, $artnr, "", 1, $product_price);
            $rabattObj->oblkSnurra();

            if ($rabattObj->_F_APRIS != -1) {
                $product_price = $rabattObj->_F_APRIS;
                
                if ($rabattObj->_F_RAB > 0) {
                    $product_price = $product_price * (100.0 - $rabattObj->_F_RAB) / 100.0;
                }
            }
        }

        if ($origPrice < $product_price) {
            return $origPrice;
        } else {
        return $product_price;
        }
    }
}
