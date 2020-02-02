<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date  2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

class Sh404sefClassConfig
{

	const CAT_ALL_NESTED_CAT = 0;
	const CAT_FIRST          = 1;
	const CAT_LAST           = 2;
	const CAT_2_FIRST        = 3;
	const CAT_2_LAST         = 4;
	const CAT_NONE           = 5;

	const DONT_ENFORCE_WWW = 0;
	const ENFORCE_WWW      = 1;
	const ENFORCE_NO_WWW   = 2;

	const COM_SH404SEF_UNCATEGORIZED_EMPTY         = 0;
	const COM_SH404SEF_UNCATEGORIZED_USE_MENU_ITEM = 1;

	const COM_SH404SEF_KEYSTORE_TABLE_NAME         = '#__sh404sef_keystore';
	const COM_SH404SEF_KEYSTORE_KEY_404_ERROR_PAGE = 'com_sh404sef.errors.404';

	/* string,  version number */
	public $version = '4.7.3.3292';
	/* boolean, is 404 SEF enabled  */
	public $Enabled = false;
	/* char,  Character to use for url replacement */
	public $replacement = '-';
	/* char,  Character to use for page spacer */
	public $pagerep = '-';
	/* strip these characters */
	public $stripthese = ',|~|!|@|%|^|(|)|<|>|:|;|{|}|[|]|&|`|„|‹|’|‘|“|”|•|›|«|´|»|°';
	/* characters replacement table v 1.2.4.f April 4, 2007*/
	public $shReplacements = 'Š|S, Œ|O, Ž|Z, š|s, œ|oe, ž|z, Ÿ|Y, ¥|Y, µ|u, À|A, Á|A, Â|A, Ã|A, Ä|A, Å|A, Æ|A, Ç|C, È|E, É|E, Ê|E, Ë|E, Ì|I, Í|I, Î|I, Ï|I, Ð|D, Ñ|N, Ò|O, Ó|O, Ô|O, Õ|O, Ö|O, Ø|O, Ù|U, Ú|U, Û|U, Ü|U, Ý|Y, ß|s, à|a, á|a, â|a, ã|a, ä|a, å|a, æ|a, ç|c, è|e, é|e, ê|e, ë|e, ì|i, í|i, î|i, ï|i, ð|o, ñ|n, ò|o, ó|o, ô|o, õ|o, ö|o, ø|o, ù|u, ú|u, û|u, ü|u, ý|y, ÿ|y, ß|ss, ă|a, ş|s, ţ|t, ț|t, Ț|T, Ș|S, ș|s, Ş|S';
	/* string,  suffix for "files" */
	public $suffix = '';
	/* string,  file to display when there is none */
	public $addFile = '';
	/* trims friendly characters from where they shouldn't be */
	public $friendlytrim = '-|.';
	/* boolean, convert url to lowercase */
	public $LowerCase = true;
	/* boolean, include the section name in url */
	public $ShowSection = false;
	/* boolean, exclude the category name in url */
	public $ShowCat = true;
	/* boolean, use the title_alias instead of the title */
	public $UseAlias = true;
	/* int, id of #__content item to use for static page */
	public $page404 = 0;
	/* Array, contains predefined components. DEPRECATED - 2.2.4*/
	public $predefined = array();
	/* Array, contains components 404 SEF will ignore. */
	public $skip = array();
	/* Array, contains components 404 SEF will not add to the DB.
	 * default style URLs will be generated for these components instead
	 */
	public $nocache = array('events');
	// shumisha : additional parameters
	/* Array, contains components 404 SEF will override their own sef_ext file if it has its own plugin. */
	public $shDoNotOverrideOwnSef = array();
	/* boolean,  true (default) to log 404 errors to DB, false otherwise  */
	public $shLog404Errors = true;
	/* boolean,  true (default) to use in mem cache, false to disable  */
	public $shUseURLCache = true;
	/* integer, max number of URL couple (sef + non-sef url) allowed in cache */
	public $shMaxURLInCache = 10000;
	/* boolean,  true (default) to translate texts in URL */
	public $shTranslateURL = true;
	/* boolean,  true (default) will always insert language iso code in URL (for other than default language) */
	public $shInsertLanguageCode = true;
	/* Array, contains components sh404SEF will NOT translate URLs */
	public $notTranslateURLList = array(); // V 1.2.4.m
	/* Array, contains components sh404SEF will NOT insert iso code in URL */
	public $notInsertIsoCodeList = array();
	// cache management
	/* boolean, true if insert Itemid of menu item is none exists */
	public $shInsertGlobalItemidIfNone = false;
	/* boolean, if true insert title of menu item if no Itemid exists for the URL*/
	public $shInsertTitleIfNoItemid = false;
	/* boolean, true if always insert title of menu item. URL Itemid is used, if any, or menu item title*/
	public $shAlwaysInsertMenuTitle = false;
	/* boolean, true if always append Itemid of non-sef URL (or of current menu item if none) to SEF URL */
	public $shAlwaysInsertItemid = false; // v 1.2.4.f
	/* string, default menu name, to be used if $shAlwaysInsertMenuTitle is true, to override menu title */
	public $shDefaultMenuItemName = '';
	/* boolean, if true, Getvars not used in URl will be reappend to it  */
	public $shAppendRemainingGETVars = true;

	// virtuemart management
	/* boolean, true if always insert title of shop menu item */
	public $shVmInsertShopName = false;
	/* boolean, if true, product ID will be prepended to product name */
	public $shInsertProductId = false;
	/* boolean, if true, product sku will be used instead of name */
	public $shVmUseProductSKU = false;
	/* boolean, if true, product Manufacturer name will be included in URL */
	public $shVmInsertManufacturerName = false;
	/* boolean, if true, product if will be prepended to manufacturer name */
	public $shInsertManufacturerId = false;
	/* integer, if 0, no categories will be inserted in URL for a product
	 if 1, only 'last' category will be inserted in URL
	 if 2, all nested categories will be inserted in URL */
	public $shVMInsertCategories = 1;

	/* boolean, if true, an additional text will be appended to sef URl when browsing categories
	 * ie : .../product_cat/view-all-products.html VS .../product_cat/     */
	public $shVmAdditionalText = true;
	/* boolean, if true, a flypage name will be inserted in URL     */
	public $shVmInsertFlypage = true;

	/* boolean, if true, category id will be prepended to category name */
	public $shInsertCategoryId = false;
	/* boolean, if true, numerical id will be prepended to URL, for inclusion in Googlenews  */
	public $shInsertNumericalId = false;
	/* text, list of categories of content to which numerical id should be applied  */
	public $shInsertNumericalIdCatList = '';
	/* boolean, if true, non-sef URL like index.php?option=com_content&task=view&id=12&Itemid=2 will be 301-redirected to their sef equivalent */
	public $shRedirectNonSefToSef = false;
	/* boolean, if true, Joomla sef URL like /content/view/12/61 will be 301-redirected to their sef equivalent */
	public $shRedirectJoomlaSefToSef = false;
	/* string, should be set to SSL secure URL of site if any used. No trailing / */
	public $shConfig_live_secure_site = '';
	/* boolean, if true, ed non-sef parameter will be interpreted as a iJoomla param in com_content plugin  */
	public $shActivateIJoomlaMagInContent = true;
	/* boolean, if true, issue id of iJoomla magazine will be prepended to category name */
	public $shInsertIJoomlaMagIssueId = false;
	/* boolean, if true, magazine name will be prepended to all URL */
	public $shInsertIJoomlaMagName = false;
	/* boolean, if true, magazine id will be inserted before magazine title */
	public $shInsertIJoomlaMagMagazineId = false;
	/* boolean, if true, article id will be inserted before article title */
	public $shInsertIJoomlaMagArticleId = false;

	/* boolean, if true, name of menu item leading to Community builder will be prepended to all URL */
	public $shInsertCBName = false;
	/* boolean, if true, user name will be inserte to all URL wher appropriate. Warning : this will
	 *  increase DB space used? Normally user id is still passed as a GET param (ie ...?user=245)
	 *  to save space and increase speed  */
	public $shCBInsertUserName = false;
	/* boolean, if true, id of user will be prepended to its name when previous option is activated
	 *  in case two users have the same name */
	public $shCBInsertUserId = true;
	/* boolean, if true user pseudo will be used instead of name */
	public $shCBUseUserPseudo = true;

	/* integer, default value for Itemid when using lettermand newsletter component */
	public $shLMDefaultItemid = 0;

	/* boolean, if true, default name for board will be prepended to URL */
	public $shInsertFireboardName = true;
	/* boolean, if true name of forum category will be inserted in URL */
	public $shFbInsertCategoryName = true;
	/* boolean, if true, Category id will be prepended to category name, in case 2 categories have same name */
	public $shFbInsertCategoryId = false;
	/* boolean, if true, message subject will be inserted in URL */
	public $shFbInsertMessageSubject = true;
	/* boolean, if true message id will be prepended to subject, in case 2 messages have same subject */
	public $shFbInsertMessageId = true;

	/* MyBlog parameters  V 1.2.4.r*/
	public $shInsertMyBlogName      = false;
	public $shMyBlogInsertPostId    = true;
	public $shMyBlogInsertTagId     = false;
	public $shMyBlogInsertBloggerId = true;

	/* Docman parameters  V 1.2.4.r*/
	public $shInsertDocmanName    = false;
	public $shDocmanInsertDocId   = true;
	public $shDocmanInsertDocName = true;
	/* integer, if 0, no categories will be inserted in URL for a product
	 if 1, only 'last' category will be inserted in URL
	 if 2, all nested categories will be inserted in URL */
	public $shDMInsertCategories = 1;
	/* boolean, if true, category id will be prepended to category name */
	public $shDMInsertCategoryId = false;

	/* boolean, if true, url will be urlencoded, needed for some non-latin languages */
	public $shEncodeUrl = false;

	/* boolean, if true, Itemid from url on homepage with com_content will be removed, so that com_content plugin
	 *  can try guess amore appropriate one  */
	public $guessItemidOnHomepage = false; // V 1.2.4.q
	// V 1.2.4.q : added param to force non-sef if https, as we are not through with some shared ssl servers!
	public $shForceNonSefIfHttps = false;

	// V 1.2.4.s try SEF without mod_rewrite
	// V 3.0+ mode 2, /index.php?/ removed, not compatible with using Joomla router
	public $shRewriteMode    = 1; // 0 = mod_rewrite, 1 = AcceptpathInfo index.php 2 = AcceptPathInfo index.php?
	public $shRewriteStrings = array('/', '/index.php/', '/index.php?/');

	// V1.2.4.s  record duplicate URL param
	public $shRecordDuplicates        = true;
	public $shRemoveGeneratorTag      = true;
	public $shPutH1Tags               = false;
	public $shMetaManagementActivated = true;
	public $shInsertContentTableName  = true;
	public $shContentTableName        = 'Table';

	// V 1.2.4.s auto redirect from www to non-www and vice-versa
	public $shAutoRedirectWww     = self::DONT_ENFORCE_WWW;
	public $shVmInsertProductName = true;

	// V 1.2.4.t
	/* string, exact URL for homepage, to replace the automatic one. Workaround for splash pagesNo trailing / */
	public $shForcedHomePage        = '';
	public $shInsertContentBlogName = false;
	public $shContentBlogName       = '';

	// Mosets Tree params
	public $shInsertMTreeName        = false;
	public $shMTreeInsertListingName = true;
	public $shMTreeInsertListingId   = true;
	public $shMTreePrependListingId  = true;
	/* integer, if 0, no categories will be inserted in URL for a product
	 if 1, only 'last' category will be inserted in URL
	 if 2, all nested categories will be inserted in URL */
	public $shMTreeInsertCategories = 1;
	/* boolean, if true, category id will be prepended to category name */
	public $shMTreeInsertCategoryId = false;
	public $shMTreeInsertUserName   = true;
	public $shMTreeInsertUserId     = true;

	// iJoomla NewsPortal params
	public $shInsertNewsPName  = false;
	public $shNewsPInsertCatId = false;
	public $shNewsPInsertSecId = false;

	/* Remository parameters  V 1.2.4.t*/
	public $shInsertRemoName    = false;
	public $shRemoInsertDocId   = true;
	public $shRemoInsertDocName = true;
	/* integer, if 0, no categories will be inserted in URL for a product
	 if 1, only 'last' category will be inserted in URL
	 if 2, all nested categories will be inserted in URL */
	public $shRemoInsertCategories = 1;
	/* boolean, if true, category id will be prepended to category name */
	public $shRemoInsertCategoryId = false;

	// boolean, if true, task = userProfile is accessed through mysite.com/username in CB
	public $shCBShortUserURL = false; //V 1.2.4.t

	// a set of boolean vars, to decide what to do with existing data when upgrading sh404SEF
	public $shKeepStandardURLOnUpgrade     = true; //V 1.2.4.t
	public $shKeepCustomURLOnUpgrade       = true; //V 1.2.4.t
	public $shKeepMetaDataOnUpgrade        = true; //V 1.2.4.t
	public $shKeepModulesSettingsOnUpgrade = true; //V 1.2.4.t

	// boolean, to decide whether to replace page numbering by headings in multipage articles
	public $shMultipagesTitle = true; //V 1.2.4.t

	// compatiblity variables, for sef_ext files usage from OpenSef/SEf Advance
	public $encode_page_suffix = '';
	public $encode_space_char  = '';
	public $encode_lowercase   = '';
	public $encode_strip_chars = '';
	public $spec_chars_d;
	public $spec_chars;
	public $content_page_format; // V 1.2.4.r
	public $content_page_name; // V 1.2.4.r

	// V x
	public $shKeepConfigOnUpgrade = true;

	// security parameters  V x
	public $shSecEnableSecurity      = false;
	public $shSecLogAttacks          = false;
	public $shSecOnlyNumVars         = array('itemid', 'limit', 'limitstart');
	public $shSecAlphaNumVars        = array();
	public $shSecNoProtocolVars      = array('task', 'option', 'no_html', 'mosmsg', 'lang');
	public $ipWhiteList              = '';
	public $ipBlackList              = '';
	public $uAgentWhiteList          = '';
	public $uAgentBlackList          = '';
	public $shSecCheckHoneyPot       = false;
	public $shSecHoneyPotKey         = '';
	public $shSecEntranceText        = "<p>Sorry. You are visiting this site from a suspicious IP address, which triggered our protection system.</p>
    <p>If you <strong>ARE NOT</strong> a malware robot of any kind, please accept our apologies for the inconvenience. You can access the page by clicking here : ";
	public $shSecSmellyPotText       = "The following link is here to further trap malicious internet robots, so please don't click on it : ";
	public $monthsToKeepLogs         = 1; // = 1 will keep current months log + the month before
	public $shSecActivateAntiFlood   = false;
	public $shSecAntiFloodOnlyOnPOST = false; // if true, antiflood is activated only if there is some POST data, as in a form
	public $shSecAntiFloodPeriod     = 10; // period over which requests from same IP are counted
	public $shSecAntiFloodCount      = 10; // max number of request from same IP in period above

	//public $insertSectionInBlogTableLinks = false; // default should be true, but set to false for compat reason

	/* Array, contains whether we should translate URLs per language */
	public $shLangTranslateList = array(); // V 1.2.4.m
	/* Array, contains whether we should insert iso code URLs per language */
	public $shLangInsertCodeList = array();
	/* Array, contains list of default initial URL fragement per component */
	public $defaultComponentStringList = array(); // V 1.2.4.m
	/* Array, contains pagination string, per language */
	public $pageTexts = array();

	public $shAdminInterfaceType = SH404SEF_STANDARD_ADMIN;

	// V 1.3 RC shCustomTags params
	public $shInsertNoFollowPDFPrint  = true;
	public $shInsertReadMorePageTitle = false;
	public $shMultipleH1ToH2          = false;

	// V 1.3.1 RC
	public $shVmUsingItemsPerPage = true; // set to true if using drop-down list to select number of items per page
	public $shSecCheckPOSTData    = true; // if set to yes, POST data will not be checked for mosconfig, script, base64,
	// standard vars and cmd file in img names
	public $shSecCurMonth             = 0;
	public $shSecLastUpdated          = 0;
	public $shSecTotalAttacks         = 0;
	public $shSecTotalConfigVars      = 0;
	public $shSecTotalBase64          = 0;
	public $shSecTotalScripts         = 0;
	public $shSecTotalStandardVars    = 0;
	public $shSecTotalImgTxtCmd       = 0;
	public $shSecTotalIPDenied        = 0;
	public $shSecTotalUserAgentDenied = 0;
	public $shSecTotalFlooding        = 0;
	public $shSecTotalPHP             = 0;
	public $shSecTotalPHPUserClicked  = 0;
	// com_smf params
	public $shInsertSMFName     = true;
	public $shSMFItemsPerPage   = 20;
	public $shInsertSMFBoardId  = true;
	public $shInsertSMFTopicId  = true;
	public $shinsertSMFUserName = false;
	public $shInsertSMFUserId   = true;

	// other
	public $appendToPageTitle  = '';
	public $prependToPageTitle = '';
	public $debugToLogFile     = false;
	public $debugStartedAt     = 0;
	public $debugDuration      = 3600; // time in seconds to log debug data to file. if 0, unlimited, default = 1 hour

	// V 1.3.1
	public $shInsertOutboundLinksImage = false;
	public $shImageForOutboundLinks    = 'external-black.png'; // default = black image

	// V 1.0.3
	public $defaultParamList = ''; // holds content of /administrator/components/custom.sef.php for editing

	// V 1.0.12
	public $useCatAlias  = false;
	public $useSecAlias  = false;
	public $useMenuAlias = false;

	// V 1.5.3
	public $alwaysAppendItemsPerPage = false;
	public $redirectToCorrectCaseUrl = true;

	// V 1.5.5
	public $jclInsertEventId         = false;
	public $jclInsertCategoryId      = false;
	public $jclInsertCalendarId      = false;
	public $jclInsertCalendarName    = false;
	public $jclInsertDate            = false;
	public $jclInsertDateInEventView = true;

	public $ContentTitleShowSection = false;
	public $ContentTitleShowCat     = true;
	public $ContentTitleUseAlias    = false;
	public $ContentTitleUseCatAlias = false;
	public $ContentTitleUseSecAlias = false;
	public $pageTitleSeparator      = ' | ';

	// V 1.5.7
	public $ContentTitleInsertArticleId = false;
	/* text, list of categories of content to which numerical id should be applied  */
	public $shInsertContentArticleIdCatList = '';

	// V 1.5.8

	public $shJSInsertJSName          = true;
	public $shJSShortURLToUserProfile = true;
	public $shJSInsertUsername        = true;
	public $shJSInsertUserFullName    = false;
	public $shJSInsertUserId          = false;
	public $shJSInsertGroupCategory   = true;
	public $shJSInsertGroupCategoryId = false;
	public $shJSInsertGroupId         = false;
	public $shJSInsertGroupBulletinId = false;
	public $shJSInsertDiscussionId    = true;
	public $shJSInsertMessageId       = true;
	public $shJSInsertPhotoAlbum      = true;
	public $shJSInsertPhotoAlbumId    = false;
	public $shJSInsertPhotoId         = true;
	public $shJSInsertVideoCat        = true;
	public $shJSInsertVideoCatId      = false;
	public $shJSInsertVideoId         = true;

	public $shFbInsertUserName    = true;
	public $shFbInsertUserId      = true;
	public $shFbShortUrlToProfile = true;

	public $shPageNotFoundItemid = 0;

	// V 2.0.0
	public $autoCheckNewVersion = true;
	public $error404SubTemplate = 'index';

	/**
	 * Holds whether we should create a page id
	 * for current url routing task
	 *
	 * @var boolean
	 */
	public $enablePageId     = true;
	public $compEnablePageId = array('contact', 'content', 'newsfeeds', 'poll', 'user', 'weblinks');

	// V 2.0.1
	public $analyticsEnabled              = false;
	public $analyticsReportsEnabled       = false;
	public $analyticsType                 = 'ga'; // google
	public $analyticsId                   = '';
	public $analyticsExcludeIP            = array();
	public $analyticsMaxUserLevel         = '';
	public $analyticsUser                 = '';
	public $analyticsPassword             = '';
	public $analyticsAccount              = '';
	public $analyticsProfile              = '';
	public $autoCheckNewAnalytics         = true;
	public $analyticsDashboardDateRange   = 'week';
	public $analyticsEnableTimeCollection = true;
	public $analyticsEnableUserCollection = true;
	public $analyticsDashboardDataType    = 'ga:pageviews'; // visits | unique | pageviews

	// v 2.1.4
	public $slowServer = false;

	// V 2.1.7
	//var $insertContactCat = false;

	// V 2.1.10
	public $useJoomsefRouter = array();
	public $useAcesefRouter  = array();

	// V 2.2.11
	public $insertShortlinkTag   = true;
	public $insertRevCanTag      = false;
	public $insertAltShorterTag  = false;
	public $canReadRemoteConfig  = false;
	public $stopCreatingShurls   = false;
	public $shurlBlackList       = '';
	public $shurlNonSefBlackList = '';

	// V 3.0.0
	public $includeContentCat           = self::CAT_LAST;
	public $includeContentCatCategories = self::CAT_2_LAST;
	public $contentCategoriesSuffix     = 'all';
	public $contentTitleIncludeCat      = self::CAT_ALL_NESTED_CAT;

	public $useContactCatAlias          = false;
	public $contactCategoriesSuffix     = 'all';
	public $includeContactCat           = self::CAT_NONE;
	public $includeContactCatCategories = self::CAT_LAST;

	public $useWeblinksCatAlias          = false;
	public $weblinksCategoriesSuffix     = 'all';
	public $includeWeblinksCat           = self::CAT_LAST;
	public $includeWeblinksCatCategories = self::CAT_LAST;
	public $liveSites                    = array();
	public $alternateTemplate            = '';
	public $useJoomlaRouter              = array();

	// 3.1.2
	public $slugForUncategorizedContent  = self::COM_SH404SEF_UNCATEGORIZED_EMPTY;
	public $slugForUncategorizedContact  = self::COM_SH404SEF_UNCATEGORIZED_EMPTY;
	public $slugForUncategorizedWeblinks = self::COM_SH404SEF_UNCATEGORIZED_EMPTY;

	// 3.4
	public $enableMultiLingualSupport = true;
	public $enableOpenGraphData       = false;
	public $ogEnableDescription       = true;
	public $ogType                    = 'article';
	public $ogImage                   = '';
	public $ogEnableSiteName          = true;
	public $ogSiteName                = '';
	public $ogEnableLocation          = false;
	public $ogLatitude                = '';
	public $ogLongitude               = '';
	public $ogStreetAddress           = '';
	public $ogLocality                = '';
	public $ogPostalCode              = '';
	public $ogRegion                  = '';
	public $ogCountryName             = '';
	public $ogEnableContact           = false;
	public $ogEmail                   = '';
	public $ogPhoneNumber             = '';
	public $ogFaxNumber               = '';
	public $fbAdminIds                = '';

	public $socialButtonType = 'facebook';

	public $insertPaginationTags = true;

	public $UrlCacheHandler      = 'File'; // | Sharedmemory
	public $displayUrlCacheStats = false;

	// 3.6
	public $analyticsUserGroups = null;

	// 4.0
	public $removeOtherCanonicals = true;

	// 4.3
	public $analyticsEdition          = 'none';
	public $analyticsUgaId            = '';
	public $update_credentials_access = '';
	public $update_credentials_secret = '';
	public $analyticsGtmId            = '';

	public $enableTwitterCards         = false;
	public $twitterCardsSiteAccount    = '';
	public $twitterCardsCreatorAccount = '';
	public $twitterCardsCategories     = '';

	public $enableGoogleAuthorship        = false;
	public $googleAuthorshipAuthorProfile = '';
	public $googleAuthorshipAuthorName    = '';
	public $googleAuthorshipCategories    = '';

	// 4.4
	public $analyticsEnableAnonymization = 0;
	public $enableGooglePublisher        = false;
	public $googlePublisherUrl           = '';

	// 4.4.5
	public $extensionToExtractGetVars = 'com_chronoforums,com_mijoshop';

	// 4.4.7
	public $wbgaauth_access_token  = '';
	public $wbgaauth_refresh_token = '';
	public $wbgaauth_expires_on    = 0;
	public $wbgaauth_token_type    = '';
	public $wbgaauth_client_id_key = '';

	// 4.5
	public $notFoundErrorHandling             = 1;
	public $analyticsUserGroupsDisabled       = null;
	public $insertGoogleSitename              = 1;
	public $insertGoogleSitelinksSearch       = 0;
	public $insertGoogleSitelinksSearchCustom = '';

	public $vmUseMenuItems           = 1;
	public $vmWhichProductDetailsCat = 0;

	public $autoRedirect404            = 0;
	public $autoRedirect404WithMessage = 0;

	public $error404MsgColor           = '#FFFFFF';
	public $error404MsgBackgroundColor = '#327DCB'; // #7FBA00
	public $error404MsgOpacity         = '0.9';

	public $analyticsEnableDisplayFeatures = 0;

	// 4.5.1
	public $vmWhichVmProductDetailsTitleCat = 2;
	public $vmWhichVmCategoryTitleCat       = 2;

	// 4.6
	public $insertGoogleBreadcrumb       = 0;
	public $useJoomlaRouterPhpWithItemid = array();

	// 4.7
	public $insertDate        = false;
	public $insertDateCatList = '';

	public $log404sHits    = false;
	public $logAliasesHits = false;
	public $logShurlsHits  = false;
	public $logUrlsSource  = false;

	public $referrerPolicyMeta = 'none';
	public $pageNotFoundItemids = array();
	public $analyticsEnableEnhancedLinkAttr = false;

	// End of parameters

	/**
	 * Constructor of the configuration object
	 * Tries to read config from #__extensions table params.
	 * If found, builds an object that matches the object format used previously
	 * (pre- 3.8) so as to maintain b/c compat.
	 * If not found, search for a configuration file on disk,
	 * from pre-3.8 versions of sh404SEF
	 * If found, transfers data in new object and save to #__extensions table
	 *
	 * @param boolean $reset if true, params are read again from db
	 */
	public function __construct($reset = false)
	{
		$app = JFactory::getApplication();

		// try to read from params column of com_sh404sef record in #__extensions table
		$values = Sh404sefHelperGeneral::getComponentParams($reset)->toArray();
		if (isset($values['version']))
		{
			unset($values['version']);
		}

		// if values found, read them and build an object identical to the one
		// we would have obtained from reading a config file, pre-3.8 version
		if (!empty($values))
		{
			//options names for components
			$com_options = array('manageURL', 'translateURL', 'insertIsoCode', 'shDoNotOverrideOwnSef', 'compEnablePageId', 'defaultComponentString');

			//if we have values that mean we have a json object and we can clear the values for the arrays that contain parameters related to components
			$this->nocache = array();
			$this->skip = array();
			$this->useJoomlaRouter = array();
			$this->notTranslateURLList = array();
			$this->notInsertIsoCodeList = array();
			$this->shDoNotOverrideOwnSef = array();
			$this->useJoomlaRouterPhpWithItemid = array();
			$this->useJoomsefRouter = array();
			$this->useAcesefRouter = array();
			$this->compEnablePageId = array();
			$this->shLangTranslateList = array();
			$this->shLangInsertCodeList = array();
			$this->defaultComponentStringList = array();

			foreach ($values as $key => $value)
			{
				if (property_exists(__CLASS__, $key))
				{
					$this->$key = $value;
				}
				elseif (substr_count($key, "___") && substr_count($key, "com_"))
				{
					$key_arr = explode("___", $key);
					$com_str = $key_arr[0];
					$field = $key_arr[1];
					$com_name = substr($com_str, 4);

					switch ($field)
					{
						case 'manageURL':
							switch ($value)
							{
								case 1:
									$this->nocache[] = $com_name;
									break;
								case 2:
									$this->skip[] = $com_name;
									break;
								case 3:
									$this->useJoomlaRouter[] = $com_name;
									break;
								default:
									break;
							}
							break;

						case 'translateURL':
							if ($value == 1)
							{
								$this->notTranslateURLList[] = $com_name;
							}
							break;
						case 'insertIsoCode':
							if ($value == 1)
							{
								$this->notInsertIsoCodeList[] = $com_name;
							}
							break;
						case 'shDoNotOverrideOwnSef':
							switch ($value)
							{
								case 1:
									$this->shDoNotOverrideOwnSef[] = $com_name;
									break;
								case 30:
									$this->useJoomsefRouter[] = $com_name;
									break;
								case 40:
									$this->useAcesefRouter[] = $com_name;
									break;
								case 50:
									$this->useJoomlaRouterPhpWithItemid[] = $com_name;
									break;
								default:
									break;
							}
							break;

						case 'compEnablePageId':
							if ($value == 1)
							{
								$this->compEnablePageId[] = $com_name;
							}
							break;

						case 'defaultComponentString':
							$this->defaultComponentStringList[$com_name] = $value;
							break;
					}
				}
				elseif (substr_count($key, "languages_"))
				{
					$lang_array = explode("_", $key);
					$lang = $lang_array[1];
					$lang_param = ($lang_array[2]);
					switch ($lang_param)
					{
						case 'pageText':
							$this->pageTexts[$lang] = $value;
							break;
						case 'translateURL':
							$this->shLangTranslateList[$lang] = $value;
							break;
						case 'insertCode':
							$this->shLangInsertCodeList[$lang] = $value;
							break;
						case 'notFoundItemid':
							$this->pageNotFoundItemids[$lang] = $value;
					}
				}
				elseif ($value == 'mobile_switch_enabled' || $value == 'mobile_template')
				{
				}
			}

			// -->>Need to check here is this fields shoudl still be passed throug shInitLanguageList() method
			$this->shLangTranslateList = $this->shInitLanguageList(empty($this->shLangTranslateList) ? null : $this->shLangTranslateList, 0, 0);

			$this->shLangInsertCodeList = $this->shInitLanguageList(empty($this->shLangInsertCodeList) ? null : $this->shLangInsertCodeList, 0, 0);

			$this->pageTexts = $this
				->shInitLanguageList(empty($this->pageTexts) ? null : $this->pageTexts, // V x
					isset($pagetext) ? $pagetext : 'Page-%s', isset($pagetext) ? $pagetext : 'Page-%s'); // use value from prev versions if any
			//-->
		}
		else
		{
			// no values found in params column of #__extensions table
			// either configuration was never saved, and so we keep all the defaults
			// or we're upgrading to the new DB-based configuration system
			// from a confgi file based version. Check if we can find a config file
			// and read it if so
			$sefConfigFile = sh404SEF_ADMIN_ABS_PATH . 'config/config.sef.php';

			if (shFileExists($sefConfigFile))
			{
				include($sefConfigFile);

				// shumisha : 2007-04-01 new parameters !
				if (isset($shUseURLCache))
				{
					$this->shUseURLCache = $shUseURLCache;
				}
				// shumisha : 2007-04-01 new parameters !
				if (isset($shMaxURLInCache))
				{
					$this->shMaxURLInCache = $shMaxURLInCache;
				}
				// shumisha : 2007-04-01 new parameters !
				if (isset($shTranslateURL))
				{
					$this->shTranslateURL = $shTranslateURL;
				}
				//V 1.2.4.m
				if (isset($shInsertLanguageCode))
				{
					$this->shInsertLanguageCode = $shInsertLanguageCode;
				}
				if (isset($notTranslateURLList))
				{
					$this->notTranslateURLList = $notTranslateURLList;
				}
				if (isset($notInsertIsoCodeList))
				{
					$this->notInsertIsoCodeList = $notInsertIsoCodeList;
				}

				// shumisha : 2007-04-03 new parameters !
				if (isset($shInsertGlobalItemidIfNone))
				{
					$this->shInsertGlobalItemidIfNone = $shInsertGlobalItemidIfNone;
				}
				if (isset($shInsertTitleIfNoItemid))
				{
					$this->shInsertTitleIfNoItemid = $shInsertTitleIfNoItemid;
				}
				if (isset($shAlwaysInsertMenuTitle))
				{
					$this->shAlwaysInsertMenuTitle = $shAlwaysInsertMenuTitle;
				}
				if (isset($shAlwaysInsertItemid))
				{
					$this->shAlwaysInsertItemid = $shAlwaysInsertItemid;
				}
				if (isset($shDefaultMenuItemName))
				{
					$this->shDefaultMenuItemName = $shDefaultMenuItemName;
				}
				if (isset($shAppendRemainingGETVars))
				{
					$this->shAppendRemainingGETVars = $shAppendRemainingGETVars;
				}
				if (isset($shVmInsertShopName))
				{
					$this->shVmInsertShopName = $shVmInsertShopName;
				}

				if (isset($shInsertProductId))
				{
					$this->shInsertProductId = $shInsertProductId;
				}
				if (isset($shVmUseProductSKU))
				{
					$this->shVmUseProductSKU = $shVmUseProductSKU;
				}
				if (isset($shVmInsertManufacturerName))
				{
					$this->shVmInsertManufacturerName = $shVmInsertManufacturerName;
				}
				if (isset($shInsertManufacturerId))
				{
					$this->shInsertManufacturerId = $shInsertManufacturerId;
				}
				if (isset($shVMInsertCategories))
				{
					$this->shVMInsertCategories = $shVMInsertCategories;
				}
				if (isset($shVmAdditionalText))
				{
					$this->shVmAdditionalText = $shVmAdditionalText;
				}
				if (isset($shVmInsertFlypage))
				{
					$this->shVmInsertFlypage = $shVmInsertFlypage;
				}

				if (isset($shInsertCategoryId))
				{
					$this->shInsertCategoryId = $shInsertCategoryId;
				}
				if (isset($shReplacements))
				{
					$this->shReplacements = $shReplacements;
				}

				if (isset($shInsertNumericalId))
				{
					$this->shInsertNumericalId = $shInsertNumericalId;
				}
				if (isset($shInsertNumericalIdCatList))
				{
					$this->shInsertNumericalIdCatList = $shInsertNumericalIdCatList;
				}

				if (isset($shRedirectNonSefToSef))
				{
					$this->shRedirectNonSefToSef = $shRedirectNonSefToSef;
				}
				// disabled, can't be implemented safely
				//if (isset($shRedirectJoomlaSefToSef)) $this->shRedirectJoomlaSefToSef = $shRedirectJoomlaSefToSef;
				if (isset($shConfig_live_secure_site))
				{
					$this->shConfig_live_secure_site = JString::rtrim($shConfig_live_secure_site, '/');
				}

				if (isset($shActivateIJoomlaMagInContent))
				{
					$this->shActivateIJoomlaMagInContent = $shActivateIJoomlaMagInContent;
				}
				if (isset($shInsertIJoomlaMagIssueId))
				{
					$this->shInsertIJoomlaMagIssueId = $shInsertIJoomlaMagIssueId;
				}
				if (isset($shInsertIJoomlaMagName))
				{
					$this->shInsertIJoomlaMagName = $shInsertIJoomlaMagName;
				}
				if (isset($shInsertIJoomlaMagMagazineId))
				{
					$this->shInsertIJoomlaMagMagazineId = $shInsertIJoomlaMagMagazineId;
				}
				if (isset($shInsertIJoomlaMagArticleId))
				{
					$this->shInsertIJoomlaMagArticleId = $shInsertIJoomlaMagArticleId;
				}

				if (isset($shInsertCBName))
				{
					$this->shInsertCBName = $shInsertCBName;
				}
				if (isset($shCBInsertUserName))
				{
					$this->shCBInsertUserName = $shCBInsertUserName;
				}
				if (isset($shCBInsertUserId))
				{
					$this->shCBInsertUserId = $shCBInsertUserId;
				}
				if (isset($shCBUseUserPseudo))
				{
					$this->shCBUseUserPseudo = $shCBUseUserPseudo;
				}

				if (isset($shInsertMyBlogName))
				{
					$this->shInsertMyBlogName = $shInsertMyBlogName;
				}
				if (isset($shMyBlogInsertPostId))
				{
					$this->shMyBlogInsertPostId = $shMyBlogInsertPostId;
				}
				if (isset($shMyBlogInsertTagId))
				{
					$this->shMyBlogInsertTagId = $shMyBlogInsertTagId;
				}
				if (isset($shMyBlogInsertBloggerId))
				{
					$this->shMyBlogInsertBloggerId = $shMyBlogInsertBloggerId;
				}

				if (isset($shInsertDocmanName))
				{
					$this->shInsertDocmanName = $shInsertDocmanName;
				}
				if (isset($shDocmanInsertDocId))
				{
					$this->shDocmanInsertDocId = $shDocmanInsertDocId;
				}
				if (isset($shDocmanInsertDocName))
				{
					$this->shDocmanInsertDocName = $shDocmanInsertDocName;
				}

				if (isset($shLog404Errors))
				{
					$this->shLog404Errors = $shLog404Errors;
				}

				if (isset($shLMDefaultItemid))
				{
					$this->shLMDefaultItemid = $shLMDefaultItemid;
				}

				if (isset($shInsertFireboardName))
				{
					$this->shInsertFireboardName = $shInsertFireboardName;
				}
				if (isset($shFbInsertCategoryName))
				{
					$this->shFbInsertCategoryName = $shFbInsertCategoryName;
				}
				if (isset($shFbInsertCategoryId))
				{
					$this->shFbInsertCategoryId = $shFbInsertCategoryId;
				}
				if (isset($shFbInsertMessageSubject))
				{
					$this->shFbInsertMessageSubject = $shFbInsertMessageSubject;
				}
				if (isset($shFbInsertMessageId))
				{
					$this->shFbInsertMessageId = $shFbInsertMessageId;
				}

				if (isset($shDoNotOverrideOwnSef)) // V 1.2.4.m
				{
					$this->shDoNotOverrideOwnSef = $shDoNotOverrideOwnSef;
				}

				if (isset($shEncodeUrl)) // V 1.2.4.m
				{
					$this->shEncodeUrl = $shEncodeUrl;
				}

				if (isset($guessItemidOnHomepage)) // V 1.2.4.q
				{
					$this->guessItemidOnHomepage = $guessItemidOnHomepage;
				}

				if (isset($shForceNonSefIfHttps)) // V 1.2.4.q
				{
					$this->shForceNonSefIfHttps = $shForceNonSefIfHttps;
				}

				if (isset($shRewriteMode)) // V 1.2.4.s
				{
					$this->shRewriteMode = $shRewriteMode;
				}
				if (isset($shRewriteStrings)) // V 1.2.4.s
				{
					$this->shRewriteStrings = $shRewriteStrings;
				}

				if (isset($shMetaManagementActivated)) // V 1.2.4.s
				{
					$this->shMetaManagementActivated = $shMetaManagementActivated;
				}
				if (isset($shRemoveGeneratorTag)) // V 1.2.4.s
				{
					$this->shRemoveGeneratorTag = $shRemoveGeneratorTag;
				}
				if (isset($shPutH1Tags)) // V 1.2.4.s
				{
					$this->shPutH1Tags = $shPutH1Tags;
				}
				if (isset($shInsertContentTableName)) // V 1.2.4.s
				{
					$this->shInsertContentTableName = $shInsertContentTableName;
				}
				if (isset($shContentTableName)) // V 1.2.4.s
				{
					$this->shContentTableName = $shContentTableName;
				}
				if (isset($shAutoRedirectWww)) // V 1.2.4.s
				{
					$this->shAutoRedirectWww = $shAutoRedirectWww;
				}
				if (isset($shVmInsertProductName)) // V 1.2.4.s
				{
					$this->shVmInsertProductName = $shVmInsertProductName;
				}

				if (isset($shDMInsertCategories)) // V 1.2.4.t
				{
					$this->shDMInsertCategories = $shDMInsertCategories;
				}
				if (isset($shDMInsertCategoryId)) // V 1.2.4.t
				{
					$this->shDMInsertCategoryId = $shDMInsertCategoryId;
				}

				if (isset($shForcedHomePage)) // V 1.2.4.t
				{
					$this->shForcedHomePage = $shForcedHomePage;
				}
				if (isset($shInsertContentBlogName)) // V 1.2.4.t
				{
					$this->shInsertContentBlogName = $shInsertContentBlogName;
				}
				if (isset($shContentBlogName)) // V 1.2.4.t
				{
					$this->shContentBlogName = $shContentBlogName;
				}

				if (isset($shInsertMTreeName)) // V 1.2.4.t
				{
					$this->shInsertMTreeName = $shInsertMTreeName;
				}
				if (isset($shMTreeInsertListingName)) // V 1.2.4.t
				{
					$this->shMTreeInsertListingName = $shMTreeInsertListingName;
				}
				if (isset($shMTreeInsertListingId)) // V 1.2.4.t
				{
					$this->shMTreeInsertListingId = $shMTreeInsertListingId;
				}
				if (isset($shMTreePrependListingId)) // V 1.2.4.t
				{
					$this->shMTreePrependListingId = $shMTreePrependListingId;
				}
				if (isset($shMTreeInsertCategories)) // V 1.2.4.t
				{
					$this->shMTreeInsertCategories = $shMTreeInsertCategories;
				}
				if (isset($shMTreeInsertCategoryId)) // V 1.2.4.t
				{
					$this->shMTreeInsertCategoryId = $shMTreeInsertCategoryId;
				}
				if (isset($shMTreeInsertUserName)) // V 1.2.4.t
				{
					$this->shMTreeInsertUserName = $shMTreeInsertUserName;
				}
				if (isset($shMTreeInsertUserId)) // V 1.2.4.t
				{
					$this->shMTreeInsertUserId = $shMTreeInsertUserId;
				}

				if (isset($shInsertNewsPName)) // V 1.2.4.t
				{
					$this->shInsertNewsPName = $shInsertNewsPName;
				}
				if (isset($shNewsPInsertCatId)) // V 1.2.4.t
				{
					$this->shNewsPInsertCatId = $shNewsPInsertCatId;
				}
				if (isset($shNewsPInsertSecId)) // V 1.2.4.t
				{
					$this->shNewsPInsertSecId = $shNewsPInsertSecId;
				}

				if (isset($shInsertRemoName)) // V 1.2.4.t
				{
					$this->shInsertRemoName = $shInsertRemoName;
				}
				if (isset($shRemoInsertDocId)) // V 1.2.4.t
				{
					$this->shRemoInsertDocId = $shRemoInsertDocId;
				}
				if (isset($shRemoInsertDocName)) // V 1.2.4.t
				{
					$this->shRemoInsertDocName = $shRemoInsertDocName;
				}
				if (isset($shRemoInsertCategories)) // V 1.2.4.t
				{
					$this->shRemoInsertCategories = $shRemoInsertCategories;
				}
				if (isset($shRemoInsertCategoryId)) // V 1.2.4.t
				{
					$this->shRemoInsertCategoryId = $shRemoInsertCategoryId;
				}

				if (isset($shCBShortUserURL)) // V 1.2.4.t
				{
					$this->shCBShortUserURL = $shCBShortUserURL;
				}

				if (isset($shKeepStandardURLOnUpgrade)) // V 1.2.4.t
				{
					$this->shKeepStandardURLOnUpgrade = $shKeepStandardURLOnUpgrade;
				}
				if (isset($shKeepCustomURLOnUpgrade)) // V 1.2.4.t
				{
					$this->shKeepCustomURLOnUpgrade = $shKeepCustomURLOnUpgrade;
				}
				if (isset($shKeepMetaDataOnUpgrade)) // V 1.2.4.t
				{
					$this->shKeepMetaDataOnUpgrade = $shKeepMetaDataOnUpgrade;
				}
				if (isset($shKeepModulesSettingsOnUpgrade)) // V 1.2.4.t
				{
					$this->shKeepModulesSettingsOnUpgrade = $shKeepModulesSettingsOnUpgrade;
				}

				if (isset($shMultipagesTitle)) // V 1.2.4.t
				{
					$this->shMultipagesTitle = $shMultipagesTitle;
				}

				// shumisha end of new parameters
				if (isset($Enabled))
				{
					$this->Enabled = $Enabled;
				}
				if (isset($replacement))
				{
					$this->replacement = $replacement;
				}
				if (isset($pagerep))
				{
					$this->pagerep = $pagerep;
				}
				if (isset($stripthese))
				{
					$this->stripthese = $stripthese;
				}
				if (isset($friendlytrim))
				{
					$this->friendlytrim = $friendlytrim;
				}
				if (isset($suffix))
				{
					$this->suffix = $suffix;
				}
				if (isset($addFile))
				{
					$this->addFile = $addFile;
				}
				if (isset($LowerCase))
				{
					$this->LowerCase = $LowerCase;
				}
				if (isset($HideCat))
				{
					$this->HideCat = $HideCat;
				}
				if (isset($replacement))
				{
					$this->UseAlias = $UseAlias;
				}
				if (isset($UseAlias))
				{
					$this->page404 = $page404;
				}
				if (isset($predefined))
				{
					$this->predefined = $predefined;
				}
				if (isset($skip))
				{
					$this->skip = $skip;
				}
				if (isset($nocache))
				{
					$this->nocache = $nocache;
				}

				// V x
				if (isset($shKeepConfigOnUpgrade)) // V 1.2.4.x
				{
					$this->shKeepConfigOnUpgrade = $shKeepConfigOnUpgrade;
				}
				if (isset($shSecEnableSecurity)) // V 1.2.4.x
				{
					$this->shSecEnableSecurity = $shSecEnableSecurity;
				}
				if (isset($shSecLogAttacks)) // V 1.2.4.x
				{
					$this->shSecLogAttacks = $shSecLogAttacks;
				}
				if (isset($shSecOnlyNumVars)) // V 1.2.4.x
				{
					$this->shSecOnlyNumVars = $shSecOnlyNumVars;
				}
				if (isset($shSecAlphaNumVars)) // V 1.2.4.x
				{
					$this->shSecAlphaNumVars = $shSecAlphaNumVars;
				}
				if (isset($shSecNoProtocolVars)) // V 1.2.4.x
				{
					$this->shSecNoProtocolVars = $shSecNoProtocolVars;
				}
				$this->ipWhiteList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_IP_white_list.dat');
				$this->ipBlackList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_IP_black_list.dat');
				$this->uAgentWhiteList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_uAgent_white_list.dat');
				$this->uAgentBlackList = shReadFile(sh404SEF_ADMIN_ABS_PATH . 'security/sh404SEF_uAgent_black_list.dat');

				if (isset($shSecCheckHoneyPot)) // V 1.2.4.x
				{
					$this->shSecCheckHoneyPot = $shSecCheckHoneyPot;
				}
				if (isset($shSecDebugHoneyPot)) // V 1.2.4.x
				{
					$this->shSecDebugHoneyPot = $shSecDebugHoneyPot;
				}
				if (isset($shSecHoneyPotKey)) // V 1.2.4.x
				{
					$this->shSecHoneyPotKey = $shSecHoneyPotKey;
				}
				if (isset($shSecEntranceText)) // V 1.2.4.x
				{
					$this->shSecEntranceText = $shSecEntranceText;
				}
				if (isset($shSecSmellyPotText)) // V 1.2.4.x
				{
					$this->shSecSmellyPotText = $shSecSmellyPotText;
				}
				if (isset($monthsToKeepLogs)) // V 1.2.4.x
				{
					$this->monthsToKeepLogs = $monthsToKeepLogs;
				}
				if (isset($shSecActivateAntiFlood)) // V 1.2.4.x
				{
					$this->shSecActivateAntiFlood = $shSecActivateAntiFlood;
				}
				if (isset($shSecAntiFloodOnlyOnPOST)) // V 1.2.4.x
				{
					$this->shSecAntiFloodOnlyOnPOST = $shSecAntiFloodOnlyOnPOST;
				}
				if (isset($shSecAntiFloodPeriod)) // V 1.2.4.x
				{
					$this->shSecAntiFloodPeriod = $shSecAntiFloodPeriod;
				}
				if (isset($shSecAntiFloodCount)) // V 1.2.4.x
				{
					$this->shSecAntiFloodCount = $shSecAntiFloodCount;
				}
				//  if (isset($insertSectionInBlogTableLinks))  // V 1.2.4.x
				//    $this->insertSectionInBlogTableLinks = $insertSectionInBlogTableLinks;

				$this->shLangTranslateList = $this->shInitLanguageList(isset($shLangTranslateList) ? $shLangTranslateList : null, 0, 0);
				$this->shLangInsertCodeList = $this->shInitLanguageList(isset($shLangInsertCodeList) ? $shLangInsertCodeList : null, 0, 0);

				if (isset($defaultComponentStringList)) // V 1.2.4.x
				{
					$this->defaultComponentStringList = $defaultComponentStringList;
				}

				$this->pageTexts = $this
					->shInitLanguageList(isset($pageTexts) ? $pageTexts : null, // V x
						isset($pagetext) ? $pagetext : 'Page-%s', isset($pagetext) ? $pagetext : 'Page-%s'); // use value from prev versions if any

				if (isset($shAdminInterfaceType)) // V 1.2.4.x
				{
					$this->shAdminInterfaceType = $shAdminInterfaceType;
				}

				// compatibility with version earlier than V x
				if (isset($shShopName)) // V 1.2.4.x
				{
					$this->defaultComponentStringList['virtuemart'] = $shShopName;
				}
				if (isset($shIJoomlaMagName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['magazine'] = $shIJoomlaMagName;
				}
				if (isset($shCBName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['comprofiler'] = $shCBName;
				}
				if (isset($shFireboardName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['fireboard'] = $shFireboardName;
				}
				if (isset($shMyBlogName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['myblog'] = $shMyBlogName;
				}
				if (isset($shDocmanName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['docman'] = $shDocmanName;
				}
				if (isset($shMTreeName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['mtree'] = $shMTreeName;
				}
				if (isset($shNewsPName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['news_portal'] = $shNewsPName;
				}
				if (isset($shRemoName))// V 1.2.4.x
				{
					$this->defaultComponentStringList['remository'] = $shRemoName;
				}
				// end of compatibility code

				// V 1.3 RC
				if (isset($shInsertNoFollowPDFPrint))
				{
					$this->shInsertNoFollowPDFPrint = $shInsertNoFollowPDFPrint;
				}
				if (isset($shInsertReadMorePageTitle))
				{
					$this->shInsertReadMorePageTitle = $shInsertReadMorePageTitle;
				}
				if (isset($shMultipleH1ToH2))
				{
					$this->shMultipleH1ToH2 = $shMultipleH1ToH2;
				}

				// V 1.3.1 RC
				if (isset($shVmUsingItemsPerPage))
				{
					$this->shVmUsingItemsPerPage = $shVmUsingItemsPerPage;
				}
				if (isset($shSecCheckPOSTData))
				{
					$this->shSecCheckPOSTData = $shSecCheckPOSTData;
				}
				if (isset($shSecCurMonth))
				{
					$this->shSecCurMonth = $shSecCurMonth;
				}
				if (isset($shSecLastUpdated))
				{
					$this->shSecLastUpdated = $shSecLastUpdated;
				}
				if (isset($shSecTotalAttacks))
				{
					$this->shSecTotalAttacks = $shSecTotalAttacks;
				}
				if (isset($shSecTotalConfigVars))
				{
					$this->shSecTotalConfigVars = $shSecTotalConfigVars;
				}
				if (isset($shSecTotalBase64))
				{
					$this->shSecTotalBase64 = $shSecTotalBase64;
				}
				if (isset($shSecTotalScripts))
				{
					$this->shSecTotalScripts = $shSecTotalScripts;
				}
				if (isset($shSecTotalStandardVars))
				{
					$this->shSecTotalStandardVars = $shSecTotalStandardVars;
				}
				if (isset($shSecTotalImgTxtCmd))
				{
					$this->shSecTotalImgTxtCmd = $shSecTotalImgTxtCmd;
				}
				if (isset($shSecTotalIPDenied))
				{
					$this->shSecTotalIPDenied = $shSecTotalIPDenied;
				}
				if (isset($shSecTotalUserAgentDenied))
				{
					$this->shSecTotalUserAgentDenied = $shSecTotalUserAgentDenied;
				}
				if (isset($shSecTotalFlooding))
				{
					$this->shSecTotalFlooding = $shSecTotalFlooding;
				}
				if (isset($shSecTotalPHP))
				{
					$this->shSecTotalPHP = $shSecTotalPHP;
				}
				if (isset($shSecTotalPHPUserClicked))
				{
					$this->shSecTotalPHPUserClicked = $shSecTotalPHPUserClicked;
				}

				if (isset($prependToPageTitle))
				{
					$this->prependToPageTitle = $prependToPageTitle;
				}
				if (isset($appendToPageTitle))
				{
					$this->appendToPageTitle = $appendToPageTitle;
				}

				// if (isset($debugToLogFile))
				// $this->debugToLogFile = $debugToLogFile;
				if (isset($debugStartedAt))
				{
					$this->debugStartedAt = $debugStartedAt;
				}
				if (isset($debugDuration))
				{
					$this->debugDuration = $debugDuration;
				}

				// V 1.3.1
				if (isset($shInsertOutboundLinksImage))
				{
					$this->shInsertOutboundLinksImage = $shInsertOutboundLinksImage;
				}
				if (isset($shImageForOutboundLinks))
				{
					$this->shImageForOutboundLinks = $shImageForOutboundLinks;
				}

				// V 1.0.12
				if (isset($useCatAlias))
				{
					$this->useCatAlias = $useCatAlias;
				}
				if (isset($useMenuAlias))
				{
					$this->useMenuAlias = $useMenuAlias;
				}

				// V 1.5.3
				if (isset($alwaysAppendItemsPerPage))
				{
					$this->alwaysAppendItemsPerPage = $alwaysAppendItemsPerPage;
				}
				if (isset($redirectToCorrectCaseUrl))
				{
					$this->redirectToCorrectCaseUrl = $redirectToCorrectCaseUrl;
				}

				// V 1.5.5
				if (isset($jclInsertEventId))
				{
					$this->jclInsertEventId = $jclInsertEventId;
				}
				if (isset($jclInsertCategoryId))
				{
					$this->jclInsertCategoryId = $jclInsertCategoryId;
				}
				if (isset($jclInsertCalendarId))
				{
					$this->jclInsertCalendarId = $jclInsertCalendarId;
				}
				if (isset($jclInsertCalendarName))
				{
					$this->jclInsertCalendarName = $jclInsertCalendarName;
				}
				if (isset($jclInsertDate))
				{
					$this->jclInsertDate = $jclInsertDate;
				}
				if (isset($jclInsertDateInEventView))
				{
					$this->jclInsertDateInEventView = $jclInsertDateInEventView;
				}

				if (isset($ContentTitleShowCat))
				{
					$this->ContentTitleShowCat = $ContentTitleShowCat;
				}
				if (isset($ContentTitleUseAlias))
				{
					$this->ContentTitleUseAlias = $ContentTitleUseAlias;
				}
				if (isset($ContentTitleUseCatAlias))
				{
					$this->ContentTitleUseCatAlias = $ContentTitleUseCatAlias;
				}
				if (isset($pageTitleSeparator))
				{
					$this->pageTitleSeparator = $pageTitleSeparator;
				}

				if (isset($ContentTitleInsertArticleId))
				{
					$this->ContentTitleInsertArticleId = $ContentTitleInsertArticleId;
				}
				if (isset($shInsertContentArticleIdCatList))
				{
					$this->shInsertContentArticleIdCatList = $shInsertContentArticleIdCatList;
				}

				// 1.5.8
				if (isset($shJSInsertJSName))
				{
					$this->shJSInsertJSName = $shJSInsertJSName;
				}
				if (isset($shJSShortURLToUserProfile))
				{
					$this->shJSShortURLToUserProfile = $shJSShortURLToUserProfile;
				}
				if (isset($shJSInsertUsername))
				{
					$this->shJSInsertUsername = $shJSInsertUsername;
				}
				if (isset($shJSInsertUserFullName))
				{
					$this->shJSInsertUserFullName = $shJSInsertUserFullName;
				}
				if (isset($shJSInsertUserId))
				{
					$this->shJSInsertUserId = $shJSInsertUserId;
				}
				if (isset($shJSInsertUserFullName))
				{
					$this->shJSInsertUserFullName = $shJSInsertUserFullName;
				}
				if (isset($shJSInsertGroupCategory))
				{
					$this->shJSInsertGroupCategory = $shJSInsertGroupCategory;
				}
				if (isset($shJSInsertGroupCategoryId))
				{
					$this->shJSInsertGroupCategoryId = $shJSInsertGroupCategoryId;
				}
				if (isset($shJSInsertGroupId))
				{
					$this->shJSInsertGroupId = $shJSInsertGroupId;
				}
				if (isset($shJSInsertGroupBulletinId))
				{
					$this->shJSInsertGroupBulletinId = $shJSInsertGroupBulletinId;
				}
				if (isset($shJSInsertDiscussionId))
				{
					$this->shJSInsertDiscussionId = $shJSInsertDiscussionId;
				}
				if (isset($shJSInsertMessageId))
				{
					$this->shJSInsertMessageId = $shJSInsertMessageId;
				}
				if (isset($shJSInsertPhotoAlbum))
				{
					$this->shJSInsertPhotoAlbum = $shJSInsertPhotoAlbum;
				}
				if (isset($shJSInsertPhotoAlbumId))
				{
					$this->shJSInsertPhotoAlbumId = $shJSInsertPhotoAlbumId;
				}
				if (isset($shJSInsertPhotoId))
				{
					$this->shJSInsertPhotoId = $shJSInsertPhotoId;
				}
				if (isset($shJSInsertVideoCat))
				{
					$this->shJSInsertVideoCat = $shJSInsertVideoCat;
				}
				if (isset($shJSInsertVideoCatId))
				{
					$this->shJSInsertVideoCatId = $shJSInsertVideoCatId;
				}
				if (isset($shJSInsertVideoId))
				{
					$this->shJSInsertVideoId = $shJSInsertVideoId;
				}

				if (isset($shFbInsertUserName))
				{
					$this->shFbInsertUserName = $shFbInsertUserName;
				}
				if (isset($shFbInsertUserId))
				{
					$this->shFbInsertUserId = $shFbInsertUserId;
				}
				if (isset($shFbShortUrlToProfile))
				{
					$this->shFbShortUrlToProfile = $shFbShortUrlToProfile;
				}

				if (isset($shPageNotFoundItemid))
				{
					$this->shPageNotFoundItemid = $shPageNotFoundItemid;
				}

				if (isset($autoCheckNewVersion))
				{
					$this->autoCheckNewVersion = $autoCheckNewVersion;
				}

				if (isset($error404SubTemplate))
				{
					$this->error404SubTemplate = $error404SubTemplate;
				}

				if (isset($enablePageId))
				{
					$this->enablePageId = $enablePageId;
				}
				if (isset($compEnablePageId))
				{
					$this->compEnablePageId = $compEnablePageId;
				}
				// V 2.1.0
				if (isset($analyticsEnabled))
				{
					$this->analyticsEnabled = $analyticsEnabled;
				}
				if (isset($analyticsReportsEnabled))
				{
					$this->analyticsReportsEnabled = $analyticsReportsEnabled;
				}
				if (isset($analyticsType))
				{
					$this->analyticsType = $analyticsType;
				}
				if (isset($analyticsId))
				{
					$this->analyticsId = $analyticsId;
				}
				if (isset($analyticsUser))
				{
					$this->analyticsUser = $analyticsUser;
				}
				if (isset($analyticsPassword))
				{
					$this->analyticsPassword = $analyticsPassword;
				}
				if (isset($analyticsAccount))
				{
					$this->analyticsAccount = $analyticsAccount;
				}
				if (isset($analyticsExcludeIP))
				{
					$this->analyticsExcludeIP = $analyticsExcludeIP;
				}
				if (isset($analyticsMaxUserLevel))
				{
					$this->analyticsMaxUserLevel = $analyticsMaxUserLevel;
				}
				if (isset($analyticsProfile))
				{
					$this->analyticsProfile = $analyticsProfile;
				}
				if (isset($autoCheckNewAnalytics))
				{
					$this->autoCheckNewAnalytics = $autoCheckNewAnalytics;
				}
				if (isset($analyticsDashboardDateRange))
				{
					$this->analyticsDashboardDateRange = $analyticsDashboardDateRange;
				}
				if (isset($analyticsEnableTimeCollection))
				{
					$this->analyticsEnableTimeCollection = $analyticsEnableTimeCollection;
				}
				if (isset($analyticsEnableUserCollection))
				{
					$this->analyticsEnableUserCollection = $analyticsEnableUserCollection;
				}

				if (isset($analyticsDashboardDataType))
				{
					$this->analyticsDashboardDataType = $analyticsDashboardDataType;
				}

				if (isset($slowServer))
				{
					$this->slowServer = $slowServer;
				}

				// V 2.1.10
				if (isset($useJoomsefRouter))
				{
					$this->useJoomsefRouter = $useJoomsefRouter;
				}
				if (isset($useAcesefRouter))
				{
					$this->useAcesefRouter = $useAcesefRouter;
				}

				// V 2.1.11
				if (isset($insertShortlinkTag))
				{
					$this->insertShortlinkTag = $insertShortlinkTag;
				}
				if (isset($insertRevCanTag))
				{
					$this->insertRevCanTag = $insertRevCanTag;
				}
				if (isset($insertAltShorterTag))
				{
					$this->insertAltShorterTag = $insertAltShorterTag;
				}
				if (isset($canReadRemoteConfig))
				{
					$this->canReadRemoteConfig = $canReadRemoteConfig;
				}
				if (isset($stopCreatingShurls))
				{
					$this->stopCreatingShurls = $stopCreatingShurls;
				}
				if (isset($shurlBlackList))
				{
					$this->shurlBlackList = $shurlBlackList;
				}
				if (isset($shurlNonSefBlackList))
				{
					$this->shurlNonSefBlackList = $shurlNonSefBlackList;
				}

				// V 3.0.0
				if (isset($includeContentCat))
				{
					$this->includeContentCat = $includeContentCat;
				}
				if (isset($includeContentCatCategories))
				{
					$this->includeContentCatCategories = $includeContentCatCategories;
				}
				if (isset($contentCategoriesSuffix))
				{
					$this->contentCategoriesSuffix = $contentCategoriesSuffix;
				}
				if (isset($contentTitleIncludeCat))
				{
					$this->contentTitleIncludeCat = $contentTitleIncludeCat;
				}

				if (isset($useContactCatAlias))
				{
					$this->useContactCatAlias = $useContactCatAlias;
				}
				if (isset($contactCategoriesSuffix))
				{
					$this->contactCategoriesSuffix = $contactCategoriesSuffix;
				}
				if (isset($includeContactCat))
				{
					$this->includeContactCat = $includeContactCat;
				}
				if (isset($includeContactCatCategories))
				{
					$this->includeContactCatCategories = $includeContactCatCategories;
				}

				if (isset($useWeblinksCatAlias))
				{
					$this->useWeblinksCatAlias = $useWeblinksCatAlias;
				}
				if (isset($weblinksCategoriesSuffix))
				{
					$this->weblinksCategoriesSuffix = $weblinksCategoriesSuffix;
				}

				if (isset($includeWeblinksCat))
				{
					$this->includeWeblinksCat = $includeWeblinksCat;
				}

				if (isset($includeWeblinksCatCategories))
				{
					$this->includeWeblinksCatCategories = $includeWeblinksCatCategories;
				}

				$this->liveSites = $this->shInitLanguageList(isset($liveSites) ? $liveSites : array(), '', '');

				if (isset($alternateTemplate))
				{
					$this->alternateTemplate = $alternateTemplate;
				}

				if (isset($useJoomlaRouter))
				{
					$this->useJoomlaRouter = $useJoomlaRouter;
				}

				if (isset($slugForUncategorizedContent))
				{
					$this->slugForUncategorizedContent = $slugForUncategorizedContent;
				}
				if (isset($slugForUncategorizedContact))
				{
					$this->slugForUncategorizedContact = $slugForUncategorizedContact;
				}
				if (isset($slugForUncategorizedWeblinks))
				{
					$this->slugForUncategorizedWeblinks = $slugForUncategorizedWeblinks;
				}

				// 3.4
				if (isset($enableMultiLingualSupport))
				{
					$this->enableMultiLingualSupport = $enableMultiLingualSupport;
				}

				if (isset($enableOpenGraphData))
				{
					$this->enableOpenGraphData = $enableOpenGraphData;
				}
				if (isset($ogEnableDescription))
				{
					$this->ogEnableDescription = $ogEnableDescription;
				}
				if (isset($ogType))
				{
					$this->ogType = $ogType;
				}
				if (isset($ogImage))
				{
					$this->ogImage = $ogImage;
				}
				if (isset($ogEnableSiteName))
				{
					$this->ogEnableSiteName = $ogEnableSiteName;
				}
				if (isset($ogSiteName))
				{
					$this->ogSiteName = $ogSiteName;
				}
				if (isset($ogEnableLocation))
				{
					$this->ogEnableLocation = $ogEnableLocation;
				}
				if (isset($ogLatitude))
				{
					$this->ogLatitude = $ogLatitude;
				}
				if (isset($ogLongitude))
				{
					$this->ogLongitude = $ogLongitude;
				}
				if (isset($ogStreetAddress))
				{
					$this->ogStreetAddress = $ogStreetAddress;
				}
				if (isset($ogLocality))
				{
					$this->ogLocality = $ogLocality;
				}
				if (isset($ogPostalCode))
				{
					$this->ogPostalCode = $ogPostalCode;
				}
				if (isset($ogRegion))
				{
					$this->ogRegion = $ogRegion;
				}
				if (isset($ogCountryName))
				{
					$this->ogCountryName = $ogCountryName;
				}
				if (isset($ogEnableContact))
				{
					$this->ogEnableContact = $ogEnableContact;
				}
				if (isset($ogEmail))
				{
					$this->ogEmail = $ogEmail;
				}
				if (isset($ogPhoneNumber))
				{
					$this->ogPhoneNumber = $ogPhoneNumber;
				}
				if (isset($ogFaxNumber))
				{
					$this->ogFaxNumber = $ogFaxNumber;
				}
				if (isset($fbAdminIds))
				{
					$this->fbAdminIds = $fbAdminIds;
				}

				if (isset($insertPaginationTags))
				{
					$this->insertPaginationTags = $insertPaginationTags;
				}

				if (isset($UrlCacheHandler))
				{
					$this->UrlCacheHandler = $UrlCacheHandler;
				}
				if (isset($displayUrlCacheStats))
				{
					$this->displayUrlCacheStats = $displayUrlCacheStats;
				}

				if (isset($analyticsUserGroups))
				{
					$this->analyticsUserGroups = $analyticsUserGroups;
				}

				// end of parameters
				$datas = get_object_vars($this);

				$this->saveOldValues($datas);
			}
		}

		// handle "Very advanced" params
		if (!defined('sh404SEF_PROTECT_AGAINST_DOCUMENT_TYPE_ERROR'))
		{
			// define default values for seldom used params

			// SECTION : GLOBAL PARAMETERS for sh404sef ---------------------------------------------------------------------

			$shDefaultParamsHelp['sh404SEF_PROTECT_AGAINST_DOCUMENT_TYPE_ERROR'] = '// if not 0, urls for pdf documents and rss feeds  will be only partially turned into sef urls.
								//The query string &format=pdf or &format=feed will be still be appended.
								// This will protect against malfunctions when using some plugins which makes a call
								// to JFactory::getDocument() from a onAfterInitiliaze handler
								// At this time, SEF urls are not decoded and thus the document type is set to html instead of pdf or feed
								// resulting in the home page being displayed instead of the correct document';
			$shDefaultParams['sh404SEF_PROTECT_AGAINST_DOCUMENT_TYPE_ERROR'] = 0;

			/*
			 $shDefaultParamsHelp['sh404SEF_PROTECT_AGAINST_BAD_NON_DEFAULT_LANGUAGE_MENU_HOMELINK'] =
			'// Joomla mod_mainmenu module forces usage of JURI::base() for the homepage link
			// On multilingual sites, this causes homepage link in other than default language to
			// be wrong. If the following parameter is non-zero, such a homepage link
			// will be replaced by the correct link, similar to www.mysite.com/es/ for instance';
			$shDefaultParams['sh404SEF_PROTECT_AGAINST_BAD_NON_DEFAULT_LANGUAGE_MENU_HOMELINK'] = 1;
			
			 */

			$shDefaultParamsHelp['sh404SEF_REDIRECT_IF_INDEX_PHP'] = '// if not 0, sh404SEF will do a 301 redirect from http://yoursite.com/index.php
								// or http://yoursite.com/index.php?lang=xx to http://yoursite.com/
								// this may not work on some web servers, which transform yoursite.com into
								// yoursite.com/index.php, thus creating and endless loop. If your server does
								// that, set this param to 0';
			$shDefaultParams['sh404SEF_REDIRECT_IF_INDEX_PHP'] = 1;

			$shDefaultParamsHelp['sh404SEF_NON_SEF_IF_SUPERADMIN'] = '// if superadmin logged in, force non-sef, for testing and setting up purpose';
			$shDefaultParams['sh404SEF_NON_SEF_IF_SUPERADMIN'] = 0;

			$shDefaultParamsHelp['sh404SEF_DE_ACTIVATE_LANG_AUTO_REDIRECT'] = '// set to 1 to prevent 303 auto redirect based on user language
								// use with care, will prevent language switch to work for users without javascript';
			$shDefaultParams['sh404SEF_DE_ACTIVATE_LANG_AUTO_REDIRECT'] = 1;

			$shDefaultParamsHelp['sh404SEF_CHECK_COMP_IS_INSTALLED'] = '// if 1, SEF URLs will only be built for installed components.';
			$shDefaultParams['sh404SEF_CHECK_COMP_IS_INSTALLED'] = 1;

			$shDefaultParamsHelp['sh404SEF_REDIRECT_OUTBOUND_LINKS'] = '// if 1, all outbound links on page will be reached through a redirect
								// to avoid page rank leakage';
			$shDefaultParams['sh404SEF_REDIRECT_OUTBOUND_LINKS'] = 0;

			$shDefaultParamsHelp['sh404SEF_PDF_DIR'] = '// if not empty, urls to pdf produced by Joomla will be prefixed with this
								// path. Can be : \'pdf\' or \'pdf/something\' (ie: don\'t put leading or trailing slashes)
								// Allows you to store some pre-built PDF in a directory called /pdf, with the same name
								// as a page. Such a pdf will be served directly by the web server instead of being built on
								// the fly by Joomla. This will save CPU and RAM. (only works this way if using htaccess';
			$shDefaultParams['sh404SEF_PDF_DIR'] = 'pdf';

			$shDefaultParamsHelp['SH404SEF_URL_CACHE_TTL'] = '// time to live for url cache in hours : default = 168h = 1 week
								// Set to 0 to keep cache forever';
			$shDefaultParams['SH404SEF_URL_CACHE_TTL'] = 168;

			$shDefaultParamsHelp['SH404SEF_URL_CACHE_WRITES_TO_CHECK_TTL'] = '// number of cache write before checking cache TTL.';
			$shDefaultParams['SH404SEF_URL_CACHE_WRITES_TO_CHECK_TTL'] = 1000;

			$shDefaultParamsHelp['sh404SEF_SEC_MAIL_ATTACKS_TO_ADMIN'] = '// if set to 1, an email will be send to site admin when an attack is logged
								// if the site is live, you could be drowning in email rapidly !!!';
			$shDefaultParams['sh404SEF_SEC_MAIL_ATTACKS_TO_ADMIN'] = 0;

			$shDefaultParams['sh404SEF_SEC_EMAIL_TO_ADMIN_SUBJECT'] = 'Your site %sh404SEF_404_SITE_NAME% was subject to an attack';
			$shDefaultParams['sh404SEF_SEC_EMAIL_TO_ADMIN_BODY'] = 'Hello !' . "\n\n"
				. 'This is sh404SEF security component, running at your site (%sh404SEF_404_SITE_URL%).' . "\n\n"
				. 'I have just blocked an attack on your site. Please check details below : ' . "\n"
				. '------------------------------------------------------------------------' . "\n" . '%sh404SEF_404_ATTACK_DETAILS%' . "\n"
				. '------------------------------------------------------------------------' . "\n\n" . 'Thanks for using sh404SEF!' . "\n\n";

			$shDefaultParamsHelp['SH404SEF_PAGES_TO_CLEAN_LOGS'] = '// number of pages between checks to remove old log files
								// if 1, we check at every page request';
			$shDefaultParams['SH404SEF_PAGES_TO_CLEAN_LOGS'] = 10000;

			$shDefaultParamsHelp['SH_VM_ALLOW_PRODUCTS_IN_MULTIPLE_CATS'] = '// SECTION : Virtuemart plugin parameters ----------------------------------------------------------------------------

								// set to 1 for products to have requested category name included in url
								// useful if some products are in more than one category. If param set to 0,
								// only one category will be used for all pages. Not recommended now that sh404SEF
								// automatically handle rel=canonical on such pages';

			$shDefaultParams['SH_VM_ALLOW_PRODUCTS_IN_MULTIPLE_CATS'] = 1;

			$shDefaultParamsHelp['sh404SEF_SOBI2_PARAMS_ALWAYS_INCLUDE_CATS'] = '// SECTION : SOBI2 plugin parameters ----------------------------------------------------------------------------

								// set to 1 to always include categories in SOBI2 entries
								// details pages url';
			$shDefaultParams['sh404SEF_SOBI2_PARAMS_ALWAYS_INCLUDE_CATS'] = 0;

			$shDefaultParamsHelp['sh404SEF_SOBI2_PARAMS_INCLUDE_ENTRY_ID'] = '// set to 1 so that entry id is prepended to url';
			$shDefaultParams['sh404SEF_SOBI2_PARAMS_INCLUDE_ENTRY_ID'] = 0;

			$shDefaultParamsHelp['sh404SEF_SOBI2_PARAMS_INCLUDE_CAT_ID'] = '// set to 1 so that category id is prepended to category name';
			$shDefaultParams['sh404SEF_SOBI2_PARAMS_INCLUDE_CAT_ID'] = 0;

			$shDefaultParamsHelp['SH404SEF_OTHER_DO_NOT_OVERRIDE_EXISTING_META_DATA'] = '// SECTION : Other parameters ----------------------------------------------------------------------------

								// set to 1 to stop overriding meta data with those defined
								// with sh404SEF';
			$shDefaultParams['SH404SEF_OTHER_DO_NOT_OVERRIDE_EXISTING_META_DATA'] = 1;
		}

		// b/c : try to read "very. advanced" values from disk file
		$sefCustomConfigFile = sh404SEF_ADMIN_ABS_PATH . 'custom.sef.php';
		// read user defined values, possibly recovered while upgrading
		if (JFile::exists($sefCustomConfigFile))
		{
			include($sefCustomConfigFile);
		}
		else
		{
			// if the file does not exists, we create it. That way, user still has the ability to manually customize it
			// generate string for parameter modification

			if ($app->isAdmin())
			{ // only need to modify custom params in back-end
				$this->defaultParamList = '<?php
			    // custom.sef.php : custom.configuration file for sh404SEF
			    // 4.7.3.3292 - https://weeblr.com/joomla-seo-analytics-security/sh404sef

			    // DO NOT REMOVE THIS LINE :
			    if (!defined(\'_JEXEC\')) die(\'Direct Access to this location is not allowed.\');
			    // DO NOT REMOVE THIS LINE' . "\n";

				if (!empty($shDefaultParams))
				{
					foreach ($shDefaultParams as $key => $value)
					{
						$this->defaultParamList .= "\n";
						if (!empty($shDefaultParamsHelp[$key]))
						{
							$this->defaultParamList .= $shDefaultParamsHelp[$key] . "\n";
						}
						// echo help text, if any
						$this->defaultParamList .= '$shDefaultParams[\'' . $key . '\'] = ' . (is_string($value) ? "'$value'" : $value) . ";\n";
					}
				}

				// write to disk
				$quoteGPC = get_magic_quotes_gpc();
				$paramsList = $quoteGPC ? stripslashes($this->defaultParamList) : $this->defaultParamList;
				JFile::write($sefCustomConfigFile, $paramsList);
			}
		}

		// read user set values for these params and create constants
		if (!empty($shDefaultParams))
		{
			foreach ($shDefaultParams as $key => $value)
			{
				if (!defined($key))
				{
					define($key, $value);
				}
			}
		}

		unset($shDefaultParams);
		unset($shDefaultParamsHelp);

		// compatiblity variables, for sef_ext files usage from OpenSef/SEf Advance V 1.2.4.p
		$this->encode_page_suffix = '';// if using an opensef sef_ext, we don't let  them manage suffix
		$this->encode_space_char = $this->replacement;
		$this->encode_lowercase = $this->LowerCase;
		$this->encode_strip_chars = $this->stripthese;
		$this->content_page_name = empty($this->pageTexts[Sh404sefFactory::getPageInfo()->currentLanguageTag]) ? 'Page'
			: str_replace('%s', '', $this->pageTexts[Sh404sefFactory::getPageInfo()->currentLanguageTag]); // V 1.2.4.r
		$this->content_page_format = '%s' . $this->replacement . '%d'; // V 1.2.4.r
		$shTemp = $this->shGetReplacements();
		foreach ($shTemp as $dest => $source)
		{
			$this->spec_chars_d .= $dest . ',';
			$this->spec_chars .= $source . ',';
		}
		JString::rtrim($this->spec_chars_d, ',');
		JString::rtrim($this->spec_chars, ',');

		if ($app->isAdmin())
		{
			$this->shCheckFilesAccess();
		}

		// hack: from 4.3 on, we may have 2 different analytics types
		// so we use the ->analyticsEdition param instead of analytcisEnabled
		// but we must carry on existing configuration
		// this should run only once, upon installation of 4.3+ version
		if (!empty($this->analyticsEnabled) && $this->analyticsEdition == 'none')
		{
			$this->analyticsEnabled = '0';
			$this->analyticsEdition = 'ga';
			$this->analyticsUgaId = '';

			// save to db
			$rawParams = Sh404sefHelperGeneral::getComponentParams($reset);
			$rawParams->set('analyticsEnabled', false);
			$rawParams->set('analyticsEdition', 'ga');
			$rawParams->set('analyticsUgaId', '');
			$rawParams->set('analyticsGtmId', '');
			Sh404sefHelperGeneral::saveComponentParams($rawParams);
		}

		include 'config.' . Sh404sefConfigurationEdition::$id . '.php';

		// from 4.7.0, we use Joomla URL Rewriting setting
		$j3 = version_compare(JVERSION, '3.0', 'ge');
		$joomlaUrlRewriting = (bool) ($j3 ? $app->get('sef_rewrite') : $app->getCfg('sef_rewrite'));
		$this->shRewriteMode = $joomlaUrlRewriting ? 0 : 1;
	}

	protected function shInitLanguageList($currentList, $default, $defaultLangDefault)
	{
		$ret = array();
		$app = JFactory::getApplication();
		if (!empty(Sh404sefFactory::getPageInfo()->isMultilingual) && !$app->isAdmin())
		{
			if (empty($currentList) || !isset($currentList[sh404sefFactory::getPageInfo()->currentLanguageTag]))
			{
				$ret[sh404sefFactory::getPageInfo()->currentLanguageTag] = $defaultLangDefault;
			}
			else
			{
				$ret[sh404sefFactory::getPageInfo()->currentLanguageTag] = $currentList[sh404sefFactory::getPageInfo()->currentLanguageTag];
			}
		}
		else
		{
			$activeLanguages = Sh404sefHelperLanguage::getAllInstalledLanguage(JPATH_ROOT);
			if (empty($activeLanguages))
			{
				if (empty($currentList) || !isset($currentList[sh404sefFactory::getPageInfo()->currentLanguageTag]))
				{
					$ret[sh404sefFactory::getPageInfo()->currentLanguageTag] = $defaultLangDefault;
				}
				else
				{
					$ret[sh404sefFactory::getPageInfo()->currentLanguageTag] = $currentList[sh404sefFactory::getPageInfo()->currentLanguageTag];
				}
			}
			else
			{
				foreach ($activeLanguages as $language)
				{
					if (empty($currentList) || !isset($currentList[$language['tag']]))
					{
						$ret[$language['tag']] = $language['tag'] == sh404sefFactory::getPageInfo()->currentLanguageTag ? $defaultLangDefault : $default;
					}
					else
					{
						$ret[$language['tag']] = $currentList[$language['tag']];
					}
				}
			}
		}
		return $ret;
	}

	protected function saveOldValues($data)
	{

		$app = JFactory::getApplication();
		if (!$app->isAdmin())
		{
			return false;
		}

		// An array name with the names of the arrays that contain parameters for components
		$comParams = array('nocache', 'skip', 'useJoomlaRouter', 'notTranslateURLList', 'notInsertIsoCodeList', 'shDoNotOverrideOwnSef',
		                   'useJoomsefRouter', 'useAcesefRouter', 'compEnablePageId', 'defaultComponentStringList');

		// Same thing for the language params
		$languageParams = array('pageTexts', 'shLangTranslateList', 'shLangInsertCodeList');

		$newParams = array();

		foreach ($data as $param => $val)
		{
			if (in_array($param, $comParams)) //-->just checking is it's a parameter related to components
			{
				//-->this will be mostly hardcoded and it's basically the reverse process of the one that loads the parameters from the json(database)
				//-->into the config class
				switch ($param)
				{
					case 'nocache':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___manageURL'] = 1;
						}
						break;

					case 'skip':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___manageURL'] = 2;
						}
						break;

					case 'useJoomlaRouter':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___manageURL'] = 3;
						}
						break;

					case 'notTranslateURLList':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___translateURL'] = 1;
						}
						break;

					case 'notInsertIsoCodeList':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___insertIsoCode'] = 1;
						}
						break;

					case 'shDoNotOverrideOwnSef':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___shDoNotOverrideOwnSef'] = 1;
						}
						break;
					case 'useJoomsefRouter':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___shDoNotOverrideOwnSef'] = 30;
						}
						break;

					case 'useAcesefRouter':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___shDoNotOverrideOwnSef'] = 40;
						}
						break;

					case 'compEnablePageId':
						foreach ($val as $com)
						{
							$newParams['com_' . $com . '___compEnablePageId'] = 1;
						}
						break;

					case 'defaultComponentStringList':
						foreach ($val as $com => $str)
						{
							$newParams['com_' . $com . '___defaultComponentString'] = $str;
						}
						break;
				}
				//--unseting the array param because he will be replaced the new parameters in the datas array that will become the json object
				unset($data[$param]);
			}
			elseif (in_array($param, $languageParams)) //-->checking to see if the parameter is related to languages parameters
			{
				switch ($param)
				{
					case 'pageTexts':
						foreach ($val as $lang => $paramval)
						{
							$newParams['languages_' . $lang . '_pageText'] = $paramval;
						}
						break;

					case 'shLangTranslateList':
						foreach ($val as $lang => $paramval)
						{
							$newParams['languages_' . $lang . '_translateURL'] = $paramval;
						}
						break;

					case 'shLangInsertCodeList':
						foreach ($val as $lang => $paramval)
						{
							$newParams['languages_' . $lang . '_insertCode'] = $paramval;
						}
						break;
				}
				//--unseting the array param because he will be replaced the new parameters in the datas array that will become the json object
				unset($data[$param]);
			}
			elseif ($val === false)
			{
				$data[$param] = 0;
			}
			elseif ($val === true)
			{
				$data[$param] = 1;
			}
		}

		// get plugins details
		if (Sh404sefConfigurationEdition::$id == 'full')
		{
			$plugin = JPluginHelper::getPlugin('system', 'shmobile');
			$params = new JRegistry();
			$params->loadString($plugin->params);
			$newParams['mobile_switch_enabled'] = $params->get('mobile_switch_enabled', 0);
			$newParams['mobile_template'] = $params->get('mobile_template', '');
		}

		//merging the new parameters into the datas array that will become the json object;
		$data = array_merge($data, $newParams);

		//-->this code is running also on the site part, not only to the admin, so we need to check out if the JPATH_BASE is going to administrator side

		$comConfigModel = Sh404sefHelperGeneral::getComConfigComponentModel();
		$component = $comConfigModel->getComponent();
		if (empty($component->id))
		{
			return false;
		}

		// Attempt to save the configuration.
		$configArray = array('params' => $data, 'id' => $component->id, 'option' => 'com_sh404sef');

		$comConfigModel->save($configArray);
	}

	/**
	 * Return array of URL characters to be replaced.
	 * Copied from Artio Joomsef V 1.4.0
	 *
	 * @return array
	 */

	public function shGetReplacements()
	{
		// V 1.2.4.q : initialize variable
		static $shReplacements = null;
		if (isset($shReplacements))
		{
			return $shReplacements;
		}
		$shReplacements = array();
		$items = explode(',', $this->shReplacements);
		foreach ($items as $item)
		{
			if (!empty($item))
			{ // V 1.2.4.q better protection. Returns null array if empty
				@list($src, $dst) = explode('|', JString::trim($item));
				$shReplacements[JString::trim($src)] = JString::trim($dst);
			}
		}
		return $shReplacements;
	}

	public function shCheckFilesAccess()
	{
		$files = array(
			'administrator/components/com_sh404sef'                => 'administrator/components/com_sh404sef',
			'administrator/components/com_sh404sef/custom.sef.php' => 'administrator/components/com_sh404sef/index.html',
			'administrator/components/com_sh404sef/security'       => 'administrator/components/com_sh404sef/security/index.html',
			'components/com_sh404sef/cache'                        => 'components/com_sh404sef/cache/index.html'
		);
		$htmlStatus = array();
		$boolStatus = array();
		foreach ($files as $folder => $file)
		{
			$boolStatus[$folder] = !$this->shCheckFileAccess($file);
			$htmlStatus[$folder] = !$boolStatus[$folder] ? JText::_('COM_SH404SEF_WRITEABLE') : JText::_('COM_SH404SEF_UNWRITEABLE');
		}
		$this->fileAccessStatus = $htmlStatus;

		return array('canAccess' => $boolStatus, 'html' => $htmlStatus);
	}

	public function shCheckFileAccess($fileName)
	{
		return is_readable(sh404SEF_ABS_PATH . $fileName) && is_writable(sh404SEF_ABS_PATH . $fileName);
	}

	/**
	 * Return array of URL characters to be replaced.
	 * Copied from Artio Joomsef V 1.4.0
	 *
	 * @return array
	 */

	public function shGetStripCharList()
	{
		static $shStripCharList = null;
		if (is_null($shStripCharList))
		{
			$shStripCharList = array();
			$shStripCharList = explode('|', $this->stripthese);
		}
		return $shStripCharList;
	}

	public function set($var, $val)
	{
		if (isset($this->$var))
		{
			$this->$var = $val;
			return true;
		}
		return false;
	}

	public function version()
	{
		return $this->version;
	}

}
