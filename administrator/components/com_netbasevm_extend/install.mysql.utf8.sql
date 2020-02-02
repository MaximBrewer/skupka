/*------------------------------------------------------------------------
* Advanced Virtuemart Invoices
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 2.0
-------------------------------------------------------------------------*/

/*
* Table mail sended
*/
CREATE TABLE IF NOT EXISTS `#__nborders_mailsended` (
`id` int(11) NOT NULL auto_increment,
`order_id` int(11) NOT NULL,
`order_prefix` varchar(20) NULL DEFAULT NULL,
`order_no` int(11),
`order_mailed` int(1) DEFAULT 0,
`dn_mailed` int(1) DEFAULT 0,
`order_generated` int(11) DEFAULT 0,
`dn_generated` int(11) DEFAULT 0,
`order_date` int(11) DEFAULT 0,
`order_lastchanged` int(11) DEFAULT 0,
PRIMARY KEY (`id`),
UNIQUE KEY `order_id` (`order_id`)
);

/*
* Table config
*/

CREATE TABLE IF NOT EXISTS `#__nborders_template` (
`id` int(3) NOT NULL auto_increment,
`template_header` TEXT,
`template_body` TEXT,
`template_footer` TEXT,
`template_default` TEXT,
PRIMARY KEY (`id`)
);
				