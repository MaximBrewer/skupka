<?php

/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */
class Sh404sefConfiguration
{
	/**
	 * List of search engines user agent strings
	 * @var array
	 */
	private $_searchEnginesAgents = array('B-l-i-t-z-B-O-T', 'Baiduspider', 'BlitzBot', 'btbot', 'DiamondBot', 'Exabot', 'FAST Enterprise Crawler',
		'FAST-WebCrawler/', 'g2Crawler', 'genieBot', 'Gigabot', 'Girafabot', 'Googlebot', 'ia_archiver', 'ichiro', 'Mediapartners-Google',
		'Mnogosearch', 'msnbot', 'MSRBOT', 'Nusearch Spider', 'SearchSight', 'Seekbot', 'sogou spider', 'Speedy Spider', 'Ask Jeeves/Teoma',
		'VoilaBot', 'Yahoo!', 'Slurp', 'YahooSeeker', 'YandexBot');

	/**
	 * List of tracking vars that should be removed from url when calculating canonical url or similar
	 * Note: 'hitcount' is introduced internally by Joomla! 3 vote plugin!!
	 * @var array
	 */
	private $_trackingVars = array('utm_source', 'utm_medium', 'utm_term', 'utm_content', 'utm_id', 'utm_campaign', 'gclid', 'fb_xd_bust',
		'fb_xd_fragment', 'hitcount');

	/**
	 * sizes of popup windows used in the program
	 * @var array
	 */
	private $_windowSizes = array('editurl' => array('x' => 0.75, 'y' => 0.7), 'confirm' => array('x' => 0.5, 'y' => 0.3),
		'import' => array('x' => 0.75, 'y' => 0.50), 'export' => array('x' => 0.75, 'y' => 0.5), 'duplicates' => array('x' => 0.9, 'y' => 0.8),
		'selectredirect' => array('x' => 0.9, 'y' => 0.8), 'enterredirect' => array('x' => 0.75, 'y' => 0.4),
		'configuration' => array('x' => 0.9, 'y' => 0.80), 'need_full_popup' => array('x' => 0.9, 'y' => 0.80));
	/**
	 * Length for modal title trimming
	 * @var array
	 */
	private $_modalTitleSizes = array('configuration' => array('l' => 60, 'i' => 40), 'editurl' => array('l' => 60, 'i' => 40),
		'confirm' => array('l' => 30, 'i' => 20));

	/**
	 * Specifications for user input of meta data
	 * @var array
	 */

	private $_metaDataSpecs = array(
		'metatitle' => array('maxCharacterSize' => 255, 'warningNumber' => 45, 'errorNumber' => 60, 'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metadesc' => array('maxCharacterSize' => 255, 'warningNumber' => 140, 'errorNumber' => 160, 'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metatitle-one-line' => array('maxCharacterSize' => 255, 'warningNumber' => 45, 'errorNumber' => 60, 'style' => 'shl-char-counter-one-line',
			'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metatitle-joomla-be' => array('maxCharacterSize' => 255, 'warningNumber' => 45, 'errorNumber' => 60,
			'style' => 'shl-char-counter-title-joomla-be', 'title' => 'PLG_SHLIB_CHAR_COUNTER'),
		'metadesc-joomla-be' => array('maxCharacterSize' => 255, 'warningNumber' => 140, 'errorNumber' => 160,
			'style' => 'shl-char-counter-desc-joomla-be', 'title' => 'PLG_SHLIB_CHAR_COUNTER'));

	/**
	 * List of components that should always be left as non-sef
	 *
	 * @var array
	 */
	private $_alwaysNonSefComponents = array('jce', 'akeeba', 'media', 'contenthistory', 'ajax');


	/**
	 * Google auth client id
	 * @var array
	 */
	private $_gaAuthClientIds = array(
		array('id' => '871605399670-tsgs87ka85l5ra39a96iq3v8cl3gig4b.apps.googleusercontent.com', 'secret' => 'N0MPxJuMVtnQZ_WNlBkZxjWy', 'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob', 'grant_type' => 'authorization_code')
	);

	/**
	 * List of Joomla versions for which we must force
	 * @var array
	 */
	private $_jVersionForceHomeLangCode = array('min' => '2.5.28', 'max' => '4');

	/**
	 * List of country codes, based on ISO 3166
	 * @var array
	 */
	private $_countryCodes = array('EN','AC','AD','AE','AF','AG','AI','AL','AM','AN','AO','AQ','AR','AS','AT','AU','AW','AX','AZ','BA','BB','BD','BE','BF','BG','BH','BI','BJ','BM','BN','BO','BR','BS','BT','BV','BW','BY','BZ','CA','CC','CD','CF','CG','CH','CI','CK','CL','CM','CN','CO','CR','CU','CV','CX','CY','CZ','DE','DJ','DK','DM','DO','DZ','EC','EE','EG','ER','ES','ET','FI','FJ','FK','FM','FO','FR','GA','GB','GD','GE','GF','GG','GH','GI','GL','GM','GN','GP','GQ','GR','GS','GT','GU','GW','GY','HK','HM','HN','HR','HT','HU','ID','IE','IL','IM','IN','IO','IQ','IR','IS','IT','JE','JM','JO','JP','KE','KG','KH','KI','KM','KN','KP','KR','KW','KY','KZ','LA','LB','LC','LI','LK','LR','LS','LT','LU','LV','LY','MA','MC','MD','ME','MG','MH','MK','ML','MM','MN','MO','MP','MQ','MR','MS','MT','MU','MV','MW','MX','MY','MZ','NA','NC','NE','NF','NG','NI','NL','NO','NP','NR','NU','NZ','OM','PA','PE','PF','PG','PH','PK','PL','PM','PN','PR','PT','PW','PY','QA','RE','RO','RS','RU','RW','SA','SB','SC','SD','SE','SG','SH','SI','SJ','SK','SL','SM','SN','SO','SR','ST','SV','SY','SZ','TA','TC','TD','TF','TG','TH','TJ','TK','TL','TM','TN','TO','TR','TT','TV','TW','TZ','UA','UG','UM','US','UY','UZ','VA','VC','VE','VG','VI','VN','VU','WF','WS','YE','YT','ZA','ZM','ZW');

	public function __get($name)
	{

		switch ($name)
		{
			case 'searchEnginesAgents':
			case 'trackingVars':
			case 'alwaysNonSefComponents':
			case 'gAuthClientIds':
			case 'jVersionForceHomeLangCode':
				$remoteConfig = Sh404sefHelperUpdates::getRemoteConfig(false);
				$prop = '_' . $name;
				$value = empty($remoteConfig->config[$name]) ? $this->$prop : $remoteConfig->config[$name];
				return $value;
				break;
			case 'windowSizes':
			case 'modalTitleSizes':
			case 'metaDataSpecs':
				$remoteConfig = Sh404sefHelperUpdates::getRemoteConfig(false);
				$remotes = empty($remoteConfig->config[$name]) ? array() : $remoteConfig->config[$name];
				$prop = '_' . $name;
				$value = array_merge($this->$prop, $remotes);
				return $value;
				break;
			default:
				$prop = '_' . $name;
				return $this->$prop;
				break;
		}

	}
}


