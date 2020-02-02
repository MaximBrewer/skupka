
-- --------------------------------------------------------

--
-- Table structure for table `#__vmee_plus_conditions`
--

CREATE TABLE IF NOT EXISTS `#__vmee_plus_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `cond_type` varchar(128) NOT NULL,
  `operator` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `text_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_id` (`rule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__vmee_plus_conditions`
--

INSERT IGNORE INTO `#__vmee_plus_conditions` (`id`, `rule_id`, `cond_type`, `operator`, `value`, `text_value`) VALUES
(18, 8, 'ORDER_STATUS', '=', 'C', 'Confirmed'),
(19, 8, 'ORDER_VENDOR', '=', '1', 'Washupito''s Tiendita');

-- --------------------------------------------------------

--
-- Table structure for table `#__vmee_plus_rules`
--

CREATE TABLE IF NOT EXISTS `#__vmee_plus_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `trigger_id` varchar(100) NOT NULL,
  `template_id` int(11) NOT NULL,
  `toList` varchar(1000) NOT NULL,
  `ccList` varchar(1000) NOT NULL,
  `bccList` varchar(1000) NOT NULL,
  `isEmailToAdmins` int(11) NOT NULL,
  `isEmailToStoreAdmins` tinyint(1) NOT NULL,
  `parameters` text NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `from` char(128) NOT NULL,
  `fromName` varchar(128) NOT NULL,
  `attachments` text,
  PRIMARY KEY (`id`),
  KEY `trigger_id` (`trigger_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `#__vmee_plus_rules`
--

INSERT IGNORE INTO `#__vmee_plus_rules` (`id`, `name`, `trigger_id`, `template_id`, `toList`, `ccList`, `bccList`, `isEmailToAdmins`, `isEmailToStoreAdmins`, `parameters`, `enabled`, `from`, `fromName`) VALUES
(1, 'Admin order confirmation', 'TRIGGER_ADMIN_ORDER_CONFIRMATION', 2, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(2, 'Order confirmation', 'TRIGGER_ORDER_CONFIRMATION', 1, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(3, 'Admin order status changed', 'TRIGGER_ADMIN_ORDER_STATUS_CHANGED', 17, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(4, 'Admin user registration', 'TRIGGER_ADMIN_USER_REGISTRATION', 18, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(5, 'Order status changed', 'TRIGGER_ORDER_STATUS_CHANGED', 3, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(7, 'Registration confirmation', 'TRIGGER_USER_REGISTRATION', 5, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(8, 'Product back in stock email', 'TRIGGER_WAITING_LIST', 19, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(9, 'Admin product back in stock', 'TRIGGER_ADMIN_WAITING_LIST', 20, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:0;}}}', 1, '', ''),
(12, 'Dropshipper notification - DROP SHIPPER NAME', 'TRIGGER_ORDER_STATUS_CHANGED', 10, '', '', '', 0, 0, 'a:1:{s:8:"preconds";a:1:{s:24:"disabledefaultreciepient";a:1:{s:6:"values";b:1;}}}', 0, '', '');


-- --------------------------------------------------------

--
-- Table structure for table `#__vmee_plus_templates`
--

CREATE TABLE IF NOT EXISTS `#__vmee_plus_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trigger_id` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `isDefault` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__vmee_plus_templates`
--
INSERT IGNORE INTO `#__vmee_plus_templates` (`id`, `trigger_id`, `name`, `subject`, `body`, `isDefault`) VALUES
(1, 'TRIGGER_ORDER_CONFIRMATION', 'Order confirmation', 'Hello [CUSTOMER_FIRST_NAME] - order number [ORDER_ID] from [SITENAME]', '<table style="border: 1px solid lightgrey; margin: 5px; padding: 5px; width: 100%;" border="0" cellspacing="1" cellpadding="1" align="center">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p> </p>\r\n<table style="width: 100%;" border="0" cellspacing="2" cellpadding="2">\r\n<tbody>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td class="Stil2" colspan="2"><strong>Order Information</strong></td>\r\n</tr>\r\n<tr class="Stil1">\r\n<td>Order Number:</td>\r\n<td>[ORDER_ID]</td>\r\n</tr>\r\n<tr class="Stil1">\r\n<td>Order Date:</td>\r\n<td>[ORDER_DATE]</td>\r\n</tr>\r\n<tr class="Stil1">\r\n<td>Order Status:</td>\r\n<td>[ORDER_STATUS]</td>\r\n</tr>\r\n<tr class="sectiontableheader">\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td colspan="2"><strong class="Stil2">Customer Information</strong></td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[BILL_TO_SHIP_TO]</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="Stil2" bgcolor="#cccccc">\r\n<td colspan="2"><strong>Order Items</strong></td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[ORDER_ITEMS_INFO]</td>\r\n</tr>\r\n<tr class="sectiontableheader">\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td colspan="2"><strong class="Stil2">Customer''s note:</strong></td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[CUSTOMER_NOTE]</td>\r\n</tr>\r\n<tr class="sectiontableheader">\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td><strong class="Stil2">Payment Information</strong></td>\r\n<td><strong class="Stil2">Shipping Information</strong></td>\r\n</tr>\r\n<tr>\r\n<td>[PAYMENT_INFO_DETAILS]</td>\r\n<td>[SHIPPING_INFO_DETAILS]</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p> </p>\r\n<p>Sincerely,</p>\r\n<p>[VENDOR_NAME]</p>\r\n<p>[SITEURL]</p>\r\n<p>[CONTACT_EMAIL]</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 0),
(2, 'TRIGGER_ADMIN_ORDER_CONFIRMATION', 'Admin order confirmation', 'New order [ORDER_ID] from [CONTACT_EMAIL] - [ORDER_TOTAL]', '<table style="border: 1px solid lightgrey; margin: 5px; padding: 5px; width: 100%;" border="0" cellspacing="1" cellpadding="1" align="center">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p> </p>\r\n<table style="width: 100%;" border="0" cellspacing="2" cellpadding="2">\r\n<tbody>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td class="Stil2" colspan="2"><strong>Order Information</strong></td>\r\n</tr>\r\n<tr class="Stil1">\r\n<td>Order Number:</td>\r\n<td>[ORDER_ID]</td>\r\n</tr>\r\n<tr class="Stil1">\r\n<td>Order Date:</td>\r\n<td>[ORDER_DATE]</td>\r\n</tr>\r\n<tr class="Stil1">\r\n<td>Order Status:</td>\r\n<td>[ORDER_STATUS]</td>\r\n</tr>\r\n<tr class="sectiontableheader">\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td colspan="2"><strong class="Stil2">Customer Information</strong></td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[BILL_TO_SHIP_TO]</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[BT_FIRST_NAME] [BT_LAST_NAME] [BT_ADDRESS_1]</td>\r\n</tr>\r\n<tr class="Stil2" bgcolor="#cccccc">\r\n<td colspan="2"><strong>Order Items</strong></td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[ORDER_ITEMS_INFO]</td>\r\n</tr>\r\n<tr class="sectiontableheader">\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td colspan="2"><strong class="Stil2">Customer''s note:</strong></td>\r\n</tr>\r\n<tr>\r\n<td colspan="2">[CUSTOMER_NOTE]</td>\r\n</tr>\r\n<tr class="sectiontableheader">\r\n<td colspan="2"></td>\r\n</tr>\r\n<tr class="sectiontableheader" bgcolor="#cccccc">\r\n<td><strong class="Stil2">Payment Information</strong></td>\r\n<td><strong class="Stil2">Shipping Information</strong></td>\r\n</tr>\r\n<tr>\r\n<td>[PAYMENT_INFO_DETAILS]</td>\r\n<td>[SHIPPING_INFO_DETAILS]</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>[ORDER_LINK|order link]</p>\r\n<p>Sincerely,</p>\r\n<p>[VENDOR_NAME]</p>\r\n<p>[SITEURL]</p>\r\n<p>[CONTACT_EMAIL]</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 0),
(3, 'TRIGGER_ORDER_STATUS_CHANGED', 'Order status changed', 'Hello [CUSTOMER_FIRST_NAME], Order Status Changed - [ORDER_ID]', '<table style="border: 1px solid lightgrey; margin: 5px; padding: 5px; width: 100%;" border="0" cellpadding="1" cellspacing="1" align="center">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p> </p>\r\n<p><strong>Hi [CUSTOMER_FIRST_NAME] [CUSTOMER_LAST_NAME],</strong></p>\r\n<p> </p>\r\n<p>The Status of your Order No. [ORDER_ID] has been changed.</p>\r\n<p>New Status is:</p>\r\n<p>[ORDER_STATUS] - [ORDER_STATUS_DESCRIPTION]</p>\r\n<hr />\r\n<p>[COMMENT]</p>\r\n<hr size="2" width="100%" />\r\n<p> </p>\r\n<p>[ORDER_ITEMS_INFO]</p>\r\n<p> </p>\r\n<p>To view the Order Details, please follow this link (or copy it into your browser):</p>\r\n<p>[ORDER_LINK]</p>\r\n<p> </p>\r\n<p>Sincerely,</p>\r\n<p>[VENDOR_NAME]</p>\r\n<p>[SITEURL]</p>\r\n<p>[CONTACT_EMAIL]</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 0),
(5, 'TRIGGER_USER_REGISTRATION', 'customer registration confirmation', 'Hello [CUSTOMER_NAME], registration details from [SITENAME]', '<table style="border: 1px solid lightgrey; margin: 5px; padding: 5px; width: 100%;" border="0" cellspacing="1" cellpadding="1" align="center">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p><strong>Dear [CUSTOMER_NAME],</strong></p>\r\n<p> </p>\r\n<p>Thanks for registering at the [VENDOR_NAME] store!</p>\r\n<p>The next time you visit, use the following username and password to log in. You only have to register once.</p>\r\n<p>You can now also access all the features of [VENDOR_NAME] using the information below. <br /> <br /> <strong>Username</strong>: [CUSTOMER_USER_NAME]<br /> <strong>Password</strong>: [CUSTOMER_PASSWORD]<br /> <br /> Once again, thanks for registering at [VENDOR_NAME]. We hope to see you often. <br /> <br /> Sincerely,</p>\r\n<p>[VENDOR_NAME]</p>\r\n<p>[SITENAME]</p>\r\n<p>[SITEURL]</p>\r\n<p>[CONTACT_EMAIL]</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 0),
(10, 'TRIGGER_ORDER_STATUS_CHANGED', 'dropshipper notification - DROP SHIPPER NAME', 'Order to ship for [VENDOR_NAME] - Order ID [ORDER_ID]', '<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p> </p>\r\n<p>[CUSTOMER_FIRST_NAME] [CUSTOMER_LAST_NAME] ([CUSTOMER_EMAIL]) has placed an order (Order ID: [ORDER_ID]) at [VENDOR_NAME] that includes products to be drop shipped from <span style="color: #ff0000;">DROP SHIPPER NAME</span>.</p>\r\n<p>Items details:</p>\r\n<p>[DS_ORDER_ITEMS_INFO|vendor_id|<span style="color: #ff0000;">DROP SHIPPER VENDOR ID</span>]</p>\r\n<p> </p>\r\n<p>Shipping method:</p>\r\n<p>[SHIPPING_INFO_DETAILS]</p>\r\n<p> </p>\r\n<p>Shipping address:</p>\r\n<p>[ST_TITLE] [BT_FIRST_NAME] [ST_LAST_NAME]</p>\r\n<p>[ST_ADDRESS_1]</p>\r\n<p>[ST_ADDRESS_2]</p>\r\n<p>[BT_STATE]</p>\r\n<p>[BT_CITY]</p>\r\n<p>[BT_COUNTRY], [BT_ZIP]</p>\r\n<p>[ST_PHONE_1]</p>\r\n<p>[ST_PHONE_2]</p>\r\n<p> </p>\r\n<p>Best Regards,</p>\r\n<p>[VENDOR_NAME]</p>\r\n<p>[SITEURL]</p>\r\n<p>[CONTACT_EMAIL]</p>', 0),
(17, 'TRIGGER_ADMIN_ORDER_STATUS_CHANGED', 'Admin order status changed', 'Order #[ORDER_ID] has new status - [ORDER_STATUS]', '<p>Hello [VENDOR_NAME],</p>\r\n<p>Order #[ORDER_ID] has a new status - [ORDER_STATUS]</p>\r\n<p>Comment: [COMMENT]</p>\r\n<p> </p>', 0),
(18, 'TRIGGER_ADMIN_USER_REGISTRATION', 'Admin user registration', 'New user registration at [SITENAME]', '<p>Hello [VENDOR_NAME],</p>\r\n<p>You have new user in your website:</p>\r\n<p>User name: [CUSTOMER_USER_NAME]</p>\r\n<p>First Name:[CUSTOMER_FIRST_NAME]</p>\r\n<p>Last Name: [CUSTOMER_LAST_NAME]</p>\r\n<p>Email: [CUSTOMER_EMAIL]</p>\r\n<p> </p>', 0),
(19, 'TRIGGER_WAITING_LIST', 'Product back in stock email', 'Hello [CUSTOMER_NAME] - Product back in stock', '<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p>Hello [CUSTOMER_NAME],</p>\r\n<p>We are happy to inform you that [WAITING_LIST_PRODUCT_LINK] is back in stock.</p>\r\n<p>We appreciate your patience and would like to offer you a special 10% discount on your next purchase at [SITEURL|our website]. Coupon code: [COUPON:percent|gift|10].</p>\r\n<p> </p>\r\n<p>Hope to see you soon,</p>\r\n<p>[SITENAME]</p>\r\n<p style="text-align: center;">[WAITING_LIST_PRODUCT_IMG]</p>', 0),
(20, 'TRIGGER_ADMIN_WAITING_LIST', 'Admin product back in stock', 'Product back in stock email was sent to [CUSTOMER_NAME]', '<p>[STORE_ADDRESS_FULL_HEADER]</p>\r\n<p>Product back in stock email was sent to:</p>\r\n<p>[CUSTOMER_FIRST_NAME] [CUSTOMER_LAST_NAME] - [CUSTOMER_EMAIL]</p>\r\n<p> </p>\r\n<p>Customer currently has [CUSTOMER_ORDERS_COUNT] orders, which sums up to income of [CUSTOMER_TOTAL].</p>\r\n<p> </p>\r\n<p>[VENDOR_NAME]</p>', 0);


-- --------------------------------------------------------
--
-- Table structure for table `#__vmee_plus_emails_history`
--

CREATE TABLE IF NOT EXISTS `#__vmee_plus_emails_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` char(32) NOT NULL,
  `type` varchar(100) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `date` int(11) unsigned NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `open` enum('yes','no') NOT NULL DEFAULT 'no',
  `click_through` enum('yes','no') NOT NULL DEFAULT 'no',
  `generated_income` double NOT NULL,
  `template_id` int(11) NOT NULL,
  `status` enum('success','waiting','failed') NOT NULL,
  `email` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`unique_id`),
  KEY `email` (`email`),
  KEY `date` (`date`),
  KEY `type` (`type`),
  KEY `rule_id` (`rule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__vmee_plus_emails_queue`
--

CREATE TABLE IF NOT EXISTS `#__vmee_plus_emails_queue` (
  `id` int(11) unsigned NOT NULL,
  `to` varchar(2048) NOT NULL,
  `cc` varchar(2048) DEFAULT NULL,
  `bcc` varchar(2048) DEFAULT NULL,
  `subject` varchar(1024) NOT NULL,
  `body` text NOT NULL,
  `embedded_images` varchar(4096) DEFAULT NULL,
  `from_name` varchar(1024) NOT NULL,
  `from_email` varchar(1024) NOT NULL,
  `date` int(11) NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__vmee_plus_schedule`
--

CREATE TABLE IF NOT EXISTS `#__vmee_plus_schedule` (
  `id` enum('sched','execute','license') NOT NULL,
  `last_run` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Dumping data for table `#__vmee_plus_schedule`
--

INSERT IGNORE INTO `#__vmee_plus_schedule` (`id`, `last_run`) VALUES
('sched', 0),
('execute', 0),
('license', 0);

UPDATE `#__vmee_plus_schedule` SET `last_run`= 0 WHERE `id`= 'license';
