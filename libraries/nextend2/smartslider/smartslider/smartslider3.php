<?php

class N2SS3 {

    public static $version = '3.2.4';

    public static $product = 'smartslider3';

    public static $campaign = 'smartslider3';

    public static $source = '';

    public static function getProUrlHome($params = array()) {
        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }

        return 'https://smartslider3.com/?' . http_build_query($params);
    }

    public static function getProUrlPricing($params = array()) {
        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }

        return 'https://smartslider3.com/pricing/?' . http_build_query($params);
    }

    public static function getWhyProUrl($params = array()) {
        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }
        $params['utm_campaign'] = N2SS3::$campaign;
        $params['utm_medium']   = 'smartslider-' . N2Platform::getPlatform() . '-' . (N2SSPRO ? 'pro' : 'free');


        return 'https://smartslider3.com/features/?' . http_build_query($params);
    }

    public static function getUpdateInfo() {
        return array(
            'name'   => 'smartslider3',
            'plugin' => 'nextend-smart-slider3-pro/nextend-smart-slider3-pro.php'
        );
    }

    public static function api($_posts, $returnUrl = false) {

        $posts = array(
            'product' => self::$product,
            'pro'     => N2SSPRO
        );
        $posts['domain'] = parse_url(N2Uri::getBaseUri(), PHP_URL_HOST);
        if (empty($posts['domain'])) {
            if (isset($_SERVER['HTTP_HOST'])) {

                $posts['domain'] = $_SERVER['HTTP_HOST'];
            }
            if (empty($posts['domain']) && isset($_SERVER['SERVER_NAME'])) {

                $posts['domain'] = $_SERVER['SERVER_NAME'];
            }
        }
        $posts['license'] = N2SmartsliderLicenseModel::getInstance()
                                                     ->getKey();
    
        return N2::api($_posts + $posts, $returnUrl);
    }

    public static function hasApiError($status, $data = array()) {
        extract($data);
        switch ($status) {
            case 'OK':
                return false;
            case 'PRODUCT_ASSET_NOT_AVAILABLE':
                N2Message::error(sprintf(n2_('Demo slider is not available with the following ID: %s'), $key));
                break;
            case 'ASSET_PREMIUM':
                N2Message::error('Premium sliders are available in PRO version only!');
                break;
            case 'ASSET_VERSION':
                N2Message::error('Please update your Smart Slider to the latest version to be able to import the selected sample slider!');
                break;
            case 'LICENSE_EXPIRED':
                N2Message::error('Your license key expired!');
                break;
            case 'DOMAIN_REGISTER_FAILED':
                N2Message::error('Your license key authorized on a different domain! You can move it to this domain like <a href="https://smartslider3.helpscoutdocs.com/article/1101-license#move" target="_blank">this</a>, or get new one: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>');
                break;
            case 'LICENSE_INVALID':
                N2Message::error('Your license key invalid, please enter again!');
                N2SmartsliderLicenseModel::getInstance()
                                         ->setKey('');

                return array(
                    "sliders/index"
                );
                break;
            case 'UPDATE_ERROR':
                N2Message::error('Update error, please update manually!');
                break;
            case 'PLATFORM_NOT_ALLOWED':
                N2Message::error(sprintf('Your license key is not valid for Smart Slider3 - %s!', N2Platform::getPlatformName()));
                break;
            case 'ERROR_HANDLED':
                break;
            case null:
                N2Message::error('Licensing server not reachable, try again later!');
                break;
            default:
                N2Message::error('Debug: ' . $status);
                N2Message::error('Licensing server not reachable, try again later!');
                break;
        }

        return true;
    }

    public static function showBeacon($search = '') {
        if (intval(N2SmartSliderSettings::get('beacon', 1))) {
            echo '<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!0,baseUrl:"//smartslider3.helpscoutdocs.com/"},contact:{enabled:!0,formId:"5bf2183c-77e2-11e5-8846-0e599dc12a51"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});HS.beacon.ready(function () {HS.beacon.search("' . $search . '");});</script>';
        }
    }

    public static function initLicense() {
        N2JS::addInline("new N2Classes.License('" . N2SmartsliderLicenseModel::getInstance()
                                                                             ->hasKey() . "', '" . N2SmartsliderLicenseModel::getInstance()
                                                                                                                            ->maybeActive() . "','" . N2Base::getApplication('smartslider')->router->createUrl(array('license/add')) . "');");
    
    }
}