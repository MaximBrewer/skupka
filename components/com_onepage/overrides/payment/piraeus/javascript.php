<?php

/**
 *
 * a Stripe payment Charge method:
 *
 * @version 1.01
 * @version Stripe PHP bindings from the Stripe API Libraries v1.6.2
 * @author Hervé Boinnard
 * @copyright Copyright (C) 2013 Hervé Boinnard - (C) 2012 Stephen.V. - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * http://www.puma-it.ie
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$method = $viewData['method']; 
$paymentForm = $viewData['paymentForm']; 
JHTMLOPC::script('opc_pir.js', 'components/com_onepage/overrides/payment/piraeus/', false);

