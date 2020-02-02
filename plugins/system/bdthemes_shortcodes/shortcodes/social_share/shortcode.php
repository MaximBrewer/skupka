<?php 

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_social_share extends Su_Shortcodes {

    function __construct() {
        parent::__construct();
    }
    public static function social_share( $atts= null, $content = null ) {
        $atts = su_shortcode_atts(array(
            'facebook'      => 'yes',
            'googleplus'    => 'yes',
            'twitter'       => 'yes',
            'vk'            => 'no',
            'linkedin'      => 'no',
            'pinterest'     => 'no',
            'telegram'     => 'no',
            'tumblr'        => 'no',
            'pocket'        => 'no',
            'reddit'        => 'no',
            'ok'            => 'no',
            'whatsapp'      => 'yes',
            'viber'         => 'yes',
            'scroll_reveal' => '',
            'class'         => ''
        ), $atts, 'social_share');
        

        $doc      = JFactory::getDocument();
        $app      = JFactory::getApplication();
        $title    = htmlentities($doc->getTitle());
        $sitename = $app->getCfg( 'sitename' );
        $url      = urlencode(JURI::current());
        $text     = urlencode($title.' | '.$sitename);
        $lang     = JFactory::getLanguage(); 
        $lang->load('plg_system_bdthemes_shortcodes', JPATH_ADMINISTRATOR);


        suAsset::addFile('css', 'social_share.css', __FUNCTION__);
        suAsset::addFile('js', 'social_share.js', __FUNCTION__);

        $return = '<div'.su_scroll_reveal($atts).' class="share-container clearfix '.su_ecssc($atts).'">';
        $return .= '<ul class="rrssb-buttons clearfix">';

        if($atts['facebook'] == 'yes') {
            $return .= '
            <li class="rrssb-facebook">
                <a href="https://www.facebook.com/sharer/sharer.php?u='.$url.'" class="popup">
                    <span class="rrssb-icon">
                        <svg version="1.1" id="ssFacebook" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                            <path d="M27.825,4.783c0-2.427-2.182-4.608-4.608-4.608H4.783c-2.422,0-4.608,2.182-4.608,4.608v18.434
                                c0,2.427,2.181,4.608,4.608,4.608H14V17.379h-3.379v-4.608H14v-1.795c0-3.089,2.335-5.885,5.192-5.885h3.718v4.608h-3.726
                                c-0.408,0-0.884,0.492-0.884,1.236v1.836h4.609v4.608h-4.609v10.446h4.916c2.422,0,4.608-2.188,4.608-4.608V4.783z"/>
                        </svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_FACEBOOK').'</span>
                </a>
            </li>';
        }
        if($atts['googleplus'] == 'yes') {
            $return .= '
            <li class="rrssb-googleplus">
                <a href="https://plus.google.com/share?url='.$url.'" class="popup">
                    <span class="rrssb-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M21 8.29h-1.95v2.6h-2.6v1.82h2.6v2.6H21v-2.6h2.6v-1.885H21V8.29zM7.614 10.306v2.925h3.9c-.26 1.69-1.755 2.925-3.9 2.925-2.34 0-4.29-2.016-4.29-4.354s1.885-4.353 4.29-4.353c1.104 0 2.014.326 2.794 1.105l2.08-2.08c-1.3-1.17-2.924-1.883-4.874-1.883C3.65 4.586.4 7.835.4 11.8s3.25 7.212 7.214 7.212c4.224 0 6.953-2.988 6.953-7.082 0-.52-.065-1.104-.13-1.624H7.614z"/></svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_GOOGLE_PLUS').'</span>
                </a>
            </li>';
        }
        if($atts['twitter'] == 'yes') {
            $return .= '
            <li class="rrssb-twitter">
                <a href="https://twitter.com/intent/tweet?text='.$text.' '.$url.'" class="popup">
                    <span class="rrssb-icon">
                        <svg version="1.1" id="ssTwitter" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                            <path d="M24.253,8.756C24.689,17.08,18.297,24.182,9.97,24.62c-3.122,0.162-6.219-0.646-8.861-2.32
                            c2.703,0.179,5.376-0.648,7.508-2.321c-2.072-0.247-3.818-1.661-4.489-3.638c0.801,0.128,1.62,0.076,2.399-0.155
                            C4.045,15.72,2.215,13.6,2.115,11.077c0.688,0.275,1.426,0.407,2.168,0.386c-2.135-1.65-2.729-4.621-1.394-6.965
                            C5.575,7.816,9.54,9.84,13.803,10.071c-0.842-2.739,0.694-5.64,3.434-6.482c2.018-0.623,4.212,0.044,5.546,1.683
                            c1.186-0.213,2.318-0.662,3.329-1.317c-0.385,1.256-1.247,2.312-2.399,2.942c1.048-0.106,2.069-0.394,3.019-0.851
                            C26.275,7.229,25.39,8.196,24.253,8.756z"/>
                        </svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_TWITTER').'</span>
                </a>
            </li>';
        }
        if($atts['linkedin'] == 'yes') {
            $return .= '
            <li class="rrssb-linkedin">
                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$url.'" class="popup">
                    <span class="rrssb-icon">
                        <svg version="1.1" id="ssLinkedin" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                            <path d="M25.424,15.887v8.447h-4.896v-7.882c0-1.979-0.709-3.331-2.48-3.331c-1.354,0-2.158,0.911-2.514,1.803
                            c-0.129,0.315-0.162,0.753-0.162,1.194v8.216h-4.899c0,0,0.066-13.349,0-14.731h4.899v2.088c-0.01,0.016-0.023,0.032-0.033,0.048
                            h0.033V11.69c0.65-1.002,1.812-2.435,4.414-2.435C23.008,9.254,25.424,11.361,25.424,15.887z M5.348,2.501
                            c-1.676,0-2.772,1.092-2.772,2.539c0,1.421,1.066,2.538,2.717,2.546h0.032c1.709,0,2.771-1.132,2.771-2.546
                            C8.054,3.593,7.019,2.501,5.343,2.501H5.348z M2.867,24.334h4.897V9.603H2.867V24.334z"/>
                        </svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_LINKEDIN').'</span>
                </a>
            </li>';
        }
        if($atts['pinterest'] == 'yes') {
            $return .= '
            <li class="rrssb-pinterest">
                <a href="http://pinterest.com/pin/create/button/?url='.$url.'&amp;description='.$text.'" class="popup">
                    <span class="rrssb-icon">
                       <svg version="1.1" id="ssPinterest" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                            <path d="M14.021,1.57C6.96,1.57,1.236,7.293,1.236,14.355c0,7.062,5.724,12.785,12.785,12.785c7.061,0,12.785-5.725,12.785-12.785
                            C26.807,7.294,21.082,1.57,14.021,1.57z M15.261,18.655c-1.161-0.09-1.649-0.666-2.559-1.219c-0.501,2.626-1.113,5.145-2.925,6.458
                            c-0.559-3.971,0.822-6.951,1.462-10.116c-1.093-1.84,0.132-5.545,2.438-4.632c2.837,1.123-2.458,6.842,1.099,7.557
                            c3.711,0.744,5.227-6.439,2.925-8.775c-3.325-3.374-9.678-0.077-8.897,4.754c0.19,1.178,1.408,1.538,0.489,3.168
                            C7.165,15.378,6.53,13.7,6.611,11.462c0.131-3.662,3.291-6.227,6.46-6.582c4.007-0.448,7.771,1.474,8.29,5.239
                            c0.579,4.255-1.816,8.865-6.102,8.533L15.261,18.655z"/>
                        </svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_PINTEREST').'</span>
                </a>
            </li>';
        }
        if($atts['tumblr'] == 'yes') {
            $return .= '
            <li class="rrssb-tumblr">
                <a href="http://tumblr.com/share?s=&amp;v=3&amp;t='.$text.'&amp;u='.$url.'" class="popup">
                    <span class="rrssb-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve"><path d="M18.02 21.842c-2.029 0.052-2.422-1.396-2.439-2.446v-7.294h4.729V7.874h-4.71V1.592c0 0-3.653 0-3.714 0 s-0.167 0.053-0.182 0.186c-0.218 1.935-1.144 5.33-4.988 6.688v3.637h2.927v7.677c0 2.8 1.7 6.7 7.3 6.6 c1.863-0.03 3.934-0.795 4.392-1.453l-1.22-3.539C19.595 21.6 18.7 21.8 18 21.842z"/>
                        </svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_TUMBLR').'</span>
                </a>
            </li>';
        }
        if($atts['pocket'] == 'yes') {
            $return .= '
            <li class="rrssb-pocket">
                <a href="https://getpocket.com/save?url='.$url.'" class="popup">
                    <span class="rrssb-icon">
                        <svg width="32px" height="28px" viewBox="0 0 32 28" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path d="M28.7817528,0.00172488695 C30.8117487,0.00431221738 31.9749312,1.12074529 31.9644402,3.10781507 C31.942147,6.67703739 32.1336065,10.2669583 31.8057648,13.8090137 C30.7147076,25.5813672 17.2181194,31.8996281 7.20714461,25.3808491 C2.71833574,22.4571656 0.196577202,18.3122624 0.0549495772,12.9357897 C-0.0342233715,9.5774348 0.00642900214,6.21519891 0.0300336062,2.85555035 C0.0405245414,1.1129833 1.21157517,0.0146615391 3.01995012,0.00819321302 C7.34746087,-0.00603710433 11.6775944,0.00431221738 16.0064164,0.00172488695 C20.2644248,0.00172488695 24.5237444,-0.00215610869 28.7817528,0.00172488695 L28.7817528,0.00172488695 Z M8.64885184,7.85611511 C7.38773662,7.99113854 6.66148108,8.42606978 6.29310958,9.33228474 C5.90114134,10.2969233 6.17774769,11.1421181 6.89875951,11.8276216 C9.35282156,14.161969 11.8108164,16.4924215 14.2976518,18.7943114 C15.3844131,19.7966007 16.5354102,19.7836177 17.6116843,18.7813283 C20.0185529,16.5495467 22.4070683,14.2982907 24.7824746,12.0327533 C25.9845979,10.8850542 26.1012707,9.56468083 25.1469132,8.60653379 C24.1361858,7.59255976 22.8449191,7.6743528 21.5890476,8.85191291 C19.9936451,10.3488554 18.3680912,11.8172352 16.8395462,13.3777945 C16.1342655,14.093159 15.7200114,14.0048744 15.0566806,13.3440386 C13.4599671,11.7484252 11.8081945,10.2060421 10.1262706,8.70001155 C9.65564653,8.27936164 9.00411403,8.05345704 8.64885184,7.85611511 L8.64885184,7.85611511 L8.64885184,7.85611511 Z"></path>
                        </svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_POCKET').'</span>
                </a>
            </li>';
        }
        if($atts['reddit'] == 'yes') {
            $return .= '
            <li class="rrssb-reddit">
                <a href="http://www.reddit.com/submit?url='.$url.'">
                    <span class="rrssb-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve"><g><path d="M11.794 15.316c0-1.029-0.835-1.895-1.866-1.895c-1.03 0-1.893 0.865-1.893 1.895s0.863 1.9 1.9 1.9 C10.958 17.2 11.8 16.3 11.8 15.316z"/><path d="M18.1 13.422c-1.029 0-1.895 0.864-1.895 1.895c0 1 0.9 1.9 1.9 1.865c1.031 0 1.869-0.836 1.869-1.865 C19.969 14.3 19.1 13.4 18.1 13.422z"/><path d="M17.527 19.791c-0.678 0.678-1.826 1.006-3.514 1.006c-0.004 0-0.009 0-0.014 0c-0.004 0-0.01 0-0.015 0 c-1.686 0-2.834-0.328-3.51-1.005c-0.264-0.265-0.693-0.265-0.958 0c-0.264 0.265-0.264 0.7 0 1 c0.943 0.9 2.4 1.4 4.5 1.402c0.005 0 0 0 0 0c0.005 0 0 0 0 0c2.066 0 3.527-0.459 4.47-1.402 c0.265-0.264 0.265-0.693 0.002-0.958C18.221 19.5 17.8 19.5 17.5 19.791z"/><path d="M27.707 13.267c0-1.785-1.453-3.237-3.236-3.237c-0.793 0-1.518 0.287-2.082 0.761c-2.039-1.295-4.646-2.069-7.438-2.219 l1.483-4.691l4.062 0.956c0.071 1.4 1.3 2.6 2.7 2.555c1.488 0 2.695-1.208 2.695-2.695C25.881 3.2 24.7 2 23.2 2 c-1.059 0-1.979 0.616-2.42 1.508l-4.633-1.091c-0.344-0.081-0.693 0.118-0.803 0.455l-1.793 5.7 C10.548 8.6 7.7 9.4 5.6 10.75C5.006 10.3 4.3 10 3.5 10.029c-1.785 0-3.237 1.452-3.237 3.2 c0 1.1 0.6 2.1 1.4 2.69c-0.04 0.272-0.061 0.551-0.061 0.831c0 2.3 1.3 4.4 3.7 5.9 c2.299 1.5 5.3 2.3 8.6 2.325c3.228 0 6.271-0.825 8.571-2.325c2.387-1.56 3.7-3.66 3.7-5.917 c0-0.26-0.016-0.514-0.051-0.768C27.088 15.5 27.7 14.4 27.7 13.267z M23.186 3.355c0.74 0 1.3 0.6 1.3 1.3 c0 0.738-0.6 1.34-1.34 1.34s-1.342-0.602-1.342-1.34C21.844 4 22.4 3.4 23.2 3.355z M1.648 13.3 c0-1.038 0.844-1.882 1.882-1.882c0.31 0 0.6 0.1 0.9 0.209c-1.049 0.868-1.813 1.861-2.26 2.9 C1.832 14.2 1.6 13.8 1.6 13.267z M21.773 21.57c-2.082 1.357-4.863 2.105-7.831 2.105c-2.967 0-5.747-0.748-7.828-2.105 c-1.991-1.301-3.088-3-3.088-4.782c0-1.784 1.097-3.484 3.088-4.784c2.081-1.358 4.861-2.106 7.828-2.106 c2.967 0 5.7 0.7 7.8 2.106c1.99 1.3 3.1 3 3.1 4.784C24.859 18.6 23.8 20.3 21.8 21.57z M25.787 14.6 c-0.432-1.084-1.191-2.095-2.244-2.977c0.273-0.156 0.59-0.245 0.928-0.245c1.035 0 1.9 0.8 1.9 1.9 C26.354 13.8 26.1 14.3 25.8 14.605z"/></g></svg>
                    </span>
                    <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_REDDIT').'</span>
                </a>
            </li>';
        }
        if($atts['vk'] == 'yes') {
            $return .= '
            <li class="rrssb-vk">
              <a href="http://vk.com/share.php?url='.$url.'" class="popup">
                <span class="rrssb-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="70 70 378.7 378.7"><path d="M254.998 363.106h21.217s6.408-.706 9.684-4.23c3.01-3.24 2.914-9.32 2.914-9.32s-.415-28.47 12.796-32.663c13.03-4.133 29.755 27.515 47.482 39.685 13.407 9.206 23.594 7.19 23.594 7.19l47.407-.662s24.797-1.53 13.038-21.027c-.96-1.594-6.85-14.424-35.247-40.784-29.728-27.59-25.743-23.126 10.063-70.85 21.807-29.063 30.523-46.806 27.8-54.405-2.596-7.24-18.636-5.326-18.636-5.326l-53.375.33s-3.96-.54-6.892 1.216c-2.87 1.716-4.71 5.726-4.71 5.726s-8.452 22.49-19.714 41.618c-23.77 40.357-33.274 42.494-37.16 39.984-9.037-5.842-6.78-23.462-6.78-35.983 0-39.112 5.934-55.42-11.55-59.64-5.802-1.4-10.076-2.327-24.915-2.48-19.046-.192-35.162.06-44.29 4.53-6.072 2.975-10.757 9.6-7.902 9.98 3.528.47 11.516 2.158 15.75 7.92 5.472 7.444 5.28 24.154 5.28 24.154s3.145 46.04-7.34 51.758c-7.193 3.922-17.063-4.085-38.253-40.7-10.855-18.755-19.054-39.49-19.054-39.49s-1.578-3.873-4.398-5.947c-3.42-2.51-8.2-3.307-8.2-3.307l-50.722.33s-7.612.213-10.41 3.525c-2.488 2.947-.198 9.036-.198 9.036s39.707 92.902 84.672 139.72c41.234 42.93 88.048 40.112 88.048 40.112"/></svg>
                </span>
                <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_VK').'</span>
              </a>
            </li>';
        }
        if($atts['telegram'] == 'yes') {
            $return .= '
            <li class="rrssb-telegram">
              <a href="https://telegram.me/share/url?url='.$url.'&text='.$text.'" class="popup">
                <span class="rrssb-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve" enable-background="new 0 0 300 300"><g id="XMLID_496_"><path id="XMLID_497_" d="M5.299 144.645l69.126 25.8 26.756 86.047c1.712 5.511 8.451 7.548 12.924 3.891l38.532-31.412c4.039-3.291 9.792-3.455 14.013-0.391l69.498 50.457c4.785 3.478 11.564 0.856 12.764-4.926L299.823 29.22c1.31-6.316-4.896-11.585-10.91-9.259L5.218 129.402C-1.783 132.102-1.722 142.014 5.299 144.645zM96.869 156.711l135.098-83.207c2.428-1.491 4.926 1.792 2.841 3.726L123.313 180.87c-3.919 3.648-6.447 8.53-7.163 13.829l-3.798 28.146c-0.503 3.758-5.782 4.131-6.819 0.494l-14.607-51.325C89.253 166.16 91.691 159.907 96.869 156.711z"/></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg>
                </span>
                <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_TELEGRAM').'</span>
              </a>
            </li>';
        }
        if($atts['ok'] == 'yes') {
            $return .= '
            <li class="rrssb-ok">
              <a href="https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl='.$url.'" class="popup">
                <span class="rrssb-icon">
                  <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
     width="95.481px" height="95.481px" viewBox="0 0 95.481 95.481" style="enable-background:new 0 0 95.481 95.481;"
     xml:space="preserve"><g><g><path d="M43.041,67.254c-7.402-0.772-14.076-2.595-19.79-7.064c-0.709-0.556-1.441-1.092-2.088-1.713
            c-2.501-2.402-2.753-5.153-0.774-7.988c1.693-2.426,4.535-3.075,7.489-1.682c0.572,0.27,1.117,0.607,1.639,0.969
            c10.649,7.317,25.278,7.519,35.967,0.329c1.059-0.812,2.191-1.474,3.503-1.812c2.551-0.655,4.93,0.282,6.299,2.514
            c1.564,2.549,1.544,5.037-0.383,7.016c-2.956,3.034-6.511,5.229-10.461,6.761c-3.735,1.448-7.826,2.177-11.875,2.661
            c0.611,0.665,0.899,0.992,1.281,1.376c5.498,5.524,11.02,11.025,16.5,16.566c1.867,1.888,2.257,4.229,1.229,6.425
            c-1.124,2.4-3.64,3.979-6.107,3.81c-1.563-0.108-2.782-0.886-3.865-1.977c-4.149-4.175-8.376-8.273-12.441-12.527
            c-1.183-1.237-1.752-1.003-2.796,0.071c-4.174,4.297-8.416,8.528-12.683,12.735c-1.916,1.889-4.196,2.229-6.418,1.15
            c-2.362-1.145-3.865-3.556-3.749-5.979c0.08-1.639,0.886-2.891,2.011-4.014c5.441-5.433,10.867-10.88,16.295-16.322
            C42.183,68.197,42.518,67.813,43.041,67.254z"/><path d="M47.55,48.329c-13.205-0.045-24.033-10.992-23.956-24.218C23.67,10.739,34.505-0.037,47.84,0
            c13.362,0.036,24.087,10.967,24.02,24.478C71.792,37.677,60.889,48.375,47.55,48.329z M59.551,24.143
            c-0.023-6.567-5.253-11.795-11.807-11.801c-6.609-0.007-11.886,5.316-11.835,11.943c0.049,6.542,5.324,11.733,11.896,11.709
            C54.357,35.971,59.573,30.709,59.551,24.143z"/></g></g></svg>
                </span>
                <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_OK').'</span>
              </a>
            </li>';
        }
        if($atts['whatsapp'] == 'yes') {
            $return .= '
            <li class="rrssb-whatsapp su-visible-small">
              <a href="whatsapp://send?text='.$url.'" class="popup">
                <span class="rrssb-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 90 90"><path d="M90 43.84c0 24.214-19.78 43.842-44.182 43.842a44.256 44.256 0 0 1-21.357-5.455L0 90l7.975-23.522a43.38 43.38 0 0 1-6.34-22.637C1.635 19.63 21.415 0 45.818 0 70.223 0 90 19.628 90 43.84zM45.818 6.983c-20.484 0-37.146 16.535-37.146 36.86 0 8.064 2.63 15.533 7.076 21.61l-4.64 13.688 14.274-4.537A37.122 37.122 0 0 0 45.82 80.7c20.48 0 37.145-16.533 37.145-36.857S66.3 6.983 45.818 6.983zm22.31 46.956c-.272-.447-.993-.717-2.075-1.254-1.084-.537-6.41-3.138-7.4-3.495-.993-.36-1.717-.54-2.438.536-.72 1.076-2.797 3.495-3.43 4.212-.632.72-1.263.81-2.347.27-1.082-.536-4.57-1.672-8.708-5.332-3.22-2.848-5.393-6.364-6.025-7.44-.63-1.076-.066-1.657.475-2.192.488-.482 1.084-1.255 1.625-1.882.543-.628.723-1.075 1.082-1.793.363-.718.182-1.345-.09-1.884-.27-.537-2.438-5.825-3.34-7.977-.902-2.15-1.803-1.793-2.436-1.793-.63 0-1.353-.09-2.075-.09-.722 0-1.896.27-2.89 1.344-.99 1.077-3.788 3.677-3.788 8.964 0 5.288 3.88 10.397 4.422 11.113.54.716 7.49 11.92 18.5 16.223 11.01 4.3 11.01 2.866 12.996 2.686 1.984-.18 6.406-2.6 7.312-5.107.9-2.513.9-4.664.63-5.112z"/></svg>
                </span>
                <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_WHATSAPP').'</span>
              </a>
            </li>';
        }
        if($atts['viber'] == 'yes') {
            $return .= '
            <li class="rrssb-viber su-visible-small">
              <a href="viber://send?text='.$url.'" class="popup">
                <span class="rrssb-icon">
                  <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"><style>.st0{fill:#231F20;}</style><path class="st0" d="M25.9 7.7c-.6-2.5-3.3-5.1-5.8-5.6-4-.8-8.1-.8-12.1 0-2.6.5-5.3 3.1-5.9 5.6-.7 3.4-.7 6.8 0 10.2.6 2.3 3.1 4.8 5.5 5.5v2.7c0 1 1.2 1.5 1.9.8l2.7-2.9c.6 0 1.2.1 1.8.1 2 0 4-.2 6.1-.6 2.5-.5 5.2-3.1 5.8-5.6.7-3.4.7-6.8 0-10.2zm-2.2 9.7c-.4 1.6-2.5 3.5-4.1 3.9-2.1.4-4.3.6-6.4.5h-.1c-.3.3-2 2.1-2 2.1L9 26.1c-.2.2-.4.1-.4-.2v-4.5c0-.1-.1-.1-.1-.2-1.8-.2-3.8-2.2-4.2-3.8-.7-3.1-.7-6.2 0-9.2.4-1.6 2.5-3.5 4.1-3.9 3.7-.7 7.5-.7 11.2 0 1.6.4 3.7 2.3 4.1 3.9.7 3 .7 6.1 0 9.2z"/><path class="st0" d="M17.5 19.5c-.3-.1-.5-.1-.7-.2-2.3-1-4.4-2.2-6.1-4.1-1-1.1-1.7-2.3-2.3-3.6-.3-.6-.5-1.2-.8-1.9-.2-.5.1-1.1.5-1.5.3-.4.8-.7 1.3-.9.4-.2.7-.1 1 .2.6.7 1.1 1.4 1.6 2.2.3.5.2 1.1-.3 1.4l-.3.3-.3.3c-.1.2-.1.4 0 .6.6 1.6 1.6 2.9 3.3 3.6.3.1.5.2.8.2.5-.1.7-.6 1-.9.4-.3.8-.3 1.2-.1.4.2.7.5 1.1.8.4.3.7.5 1 .8.3.3.4.6.2 1-.3.7-.8 1.3-1.5 1.6-.2.1-.4.1-.7.2-.2-.1.3-.1 0 0zM14 6.4c3 .1 5.5 2.1 6 5.1.1.5.1 1 .2 1.5 0 .2-.1.4-.3.4-.2 0-.3-.2-.4-.4 0-.4-.1-.9-.1-1.3-.3-2.2-2.1-4.1-4.3-4.5-.3-.1-.7-.1-1-.1-.2 0-.5 0-.5-.3 0-.2.1-.4.4-.4-.1 0-.1 0 0 0 3 .1-.1 0 0 0z"/><path class="st0" d="M18.6 12.3v.2c-.1.3-.5.3-.6 0v-.3c0-.6-.1-1.3-.5-1.8-.3-.6-.8-1-1.4-1.3-.4-.2-.7-.3-1.1-.3-.2 0-.3 0-.5-.1-.2 0-.3-.2-.3-.4-.2-.1 0-.3.2-.3.7 0 1.4.2 2 .5 1.2.7 2 1.7 2.2 3.1v.7c0 .1 0-.2 0 0z"/><path class="st0" d="M16.7 12.3c-.3 0-.4-.1-.4-.4 0-.2 0-.3-.1-.5-.1-.3-.2-.6-.5-.8-.1-.1-.3-.2-.4-.2-.2-.1-.4 0-.6-.1-.2 0-.3-.2-.3-.4s.2-.3.4-.3c1.2.1 2 .7 2.1 2.1v.3c0 .2-.1.3-.2.3-.2 0 .1 0 0 0z"/></svg>
                </span>
                <span class="rrssb-text">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SL_SHARE_BUTTON_VIBER').'</span>
              </a>
            </li>';
        }
        $return .='</ul>';
        $return .='</div>';
        return $return;
    }
}