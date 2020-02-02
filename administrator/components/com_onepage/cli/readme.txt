This folder is a placeholder for your CLI/CRON files. To create a new CLI file which uses OPC's Joomla initialization youc an create a new file as per this example: 

filename: /administrator/components/com_onepage/cli/clisample.php

content: 
<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class clisample {
  function __construct() {
    
  }
  function onCli() {
     //put all your code here:
	 $db = JFactory::getDBO(); 
	 $q = 'select 1 from #__virtuemart_products where 1'; 
	 $db->setQuery(); 
	 $db->query(); 
  }
}


//end of content, no closing php tag
// security: 
1. make sure you include the joomla security line to prevent direct access to the file
2. load the script per this example: 


#!/bin/bash

/* creates new order from products and already existing user ID and payment and shipping ID with an order status code P */
php7 /srv/www/rupostel.com/web/vm2/purity/administrator/components/com_onepage/cli.php \
--task=load \
--class=samplecli \
--user_id=42 \    
--myurl=https://vm2.rupostel.com/purity/ \
--override_jroot=/srv/www/rupostel.com/web/vm2/purity \
--return_status_json=0 \


/*

IMPORTANT
LOAD YOUR OWN CODE: 
--task=load \    ---> TASK TO LOAD A FILE IN /administrator/components/com_onepage/cli/
--class=samplecli \   CLASS NAME AND THE FILENAME WITHOUT .php SUFFIX TO LOAD A FILE IN /administrator/components/com_onepage/cli/samplecli.php IMPORTANT: the filename mass pass joomla's JFile::makeSafe() function and thus no special characters are allowed. The class name must be identical with the result of JFile::makeSafe()

SYSTEM-WIDE SETTINGS: 
--myurl=https://vm2.rupostel.com/purity/    ---> THIS IMITATES YOUR URL FOR CLI EXECUTION, SO THAT ALL EXTENSIONS RELYING ON URL/WWW SERVER WOULD STILL WORK PROPERLY
--override_jroot=/srv/www/rupostel.com/web/vm2/purity ---> THIS SETS YOUR JOOMLA ROOT WITH YOUR CONFIGURATION.PHP, IN CLI THE PATH OF YOUR JOOMLA INSTALLATION IS NOT ALWYAS THE SAME AS SEEN FROM THE WWW SERVER OR PHP PROCESSOR
--return_status_json=0 ---> RETURN EITHER JSON STRING FOR FURTHER PROCESSING OR TEXT RESPONSES
--debug=1 ---> WILL DISPLAY ADDITIONAL DEBUG INFORMATION

*/

/* displayes help */
php7 /srv/www/rupostel.com/web/vm2/purity/administrator/components/com_onepage/cli.php 
--override_jroot=/srv/www/rupostel.com/web/vm2/purity --debug=1


/* creates new order from products and already existing user ID and payment and shipping ID with an order status code P */
php7 /srv/www/rupostel.com/web/vm2/purity/administrator/components/com_onepage/cli.php \
--task=neworder \
--products_json='{"11830":10,"164":10,"11831":10}' \
--user_id=42 \
--order_status=P \
--myurl=https://vm2.rupostel.com/purity/ \
--override_jroot=/srv/www/rupostel.com/web/vm2/purity \
--virtuemart_paymentmethod_id=12 \
--virtuemart_shipmentmethod_id=27 \
--return_status_json=0 \
--coupon_code="parent"

/* exports all XML's per OPC Product XML Export config (heureka, google, etc.. ) */
php7 /srv/www/rupostel.com/web/vm2/purity/administrator/components/com_onepage/cli.php \
--task=xmlexport \
--myurl=https://vm2.rupostel.com/purity/ \
--return_status_json=0 \
--debug=1 \
--override_jroot=/srv/www/rupostel.com/web/vm2/purity 

/* creates an order with a coupon code "parent" */
php7 /srv/www/rupostel.com/web/vm2/purity/administrator/components/com_onepage/cli.php \
--products_json='{"11830":10,"164":10,"11831":10}' \
--user_id=42 \
--order_status=P \
--myurl=https://vm2.rupostel.com/purity/ \
--override_jroot=/srv/www/rupostel.com/web/vm2/purity \
--virtuemart_paymentmethod_id=12 \
--virtuemart_shipmentmethod_id=27 \
--return_status_json=0 \
--coupon_code="parent"


printf "\n"


#--debug=1 \
printf "\n"

