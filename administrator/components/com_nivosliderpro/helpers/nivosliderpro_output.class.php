<?php

class NivoSliderProOutput {

    private static $counter = 0;
    private $sliderID;
    private $slider;
    private $params;
    private $width;
    private $height;
    private $arrSlides;
    private $sliderJSID;
    private $putJSInBody = false;
    private $noConflictMode = false;
    //css settings.
    private $numSlides;
    private $bulletsSet;

    /**
     * 
     * output error message
     */
    private function outputErrorMessage($message) {
        ?>
        <div style="width:700px;height:130px;font-size:14px;text-align:center;padding-top:30px;border:1px solid black;">
        <?php echo $message ?>
        </div>
            <?php
        }

        /**
         * 
         * include slider files.
         */
        private function includeClientFiles() {

            //include custom css
            $args = "task=getcss&slider_id=" . $this->sliderID . "&slider_js_id=" . $this->sliderJSID;
            $args = str_replace("&", "&amp;", $args);
            $urlCustomCSS = UniteFunctionJoomla::getUrlComponent($args);

            $document = JFactory::getDocument();

            //put nivo js library
            $urlNivoJS = GlobalsNivoPro::$urlNivoPlugin . "js/jquery.nivo.slider.js";

            if ($this->putJSInBody == false)   //put nivo js in head
                $document->addScript($urlNivoJS);
            else {         //put nivo js in body
                ?>
            <script type="text/javascript" src="<?php echo $urlNivoJS ?>"></script>
            <?php
        }

        $document->addStyleSheet(GlobalsNivoPro::$urlNivoPlugin . "css/nivo-slider.css");
        $document->addStyleSheet($urlCustomCSS);
    }

    /**
     * 
     * init the slider, set all class vars
     */
    private function initSlider($sliderID) {

        //set basic vars
        $this->sliderID = $sliderID;
        $this->slider = HelperNivoPro::getSlider($sliderID);
        $this->params = $this->slider["params"];

        //set height and width
        $this->width = $this->params->get("width");
        $this->height = $this->params->get("height");
        if (empty($this->width) || empty($this->height))
            UniteFunctions::throwError("The slider should have width and height vars");
    }

    /**
     * 
     * init elements like sildes array that will be used in main output.
     */
    private function initMainOutput() {

        //set js id:
        self::$counter++;
        $this->sliderJSID = "nivo_slider_" . $this->sliderID . "_" . self::$counter;

        $this->arrSlides = HelperNivoPro::getArrSlides($this->sliderID);
    }

    /**
     * 
     * init css output elements
     */
    private function initCSSOutput() {
        $this->numSlides = HelperNivoPro::getNumSlides($this->sliderID);
    }

    /**
     * 
     * tells if the description exists or not
     */
    private function isDescExists($desc) {
        $descText = strip_tags($desc);
        $descText = trim($descText);
        $descText = iconv("UTF-8", "UTF-8//IGNORE", $descText);

        if (empty($descText) || strlen($descText) <= 2)
            return(false);

        return(true);
    }

    /**
     * 
     * output the slides
     */
    private function putSlides() {
		
    	//dmp($this->arrSlides);exit();
		
        foreach ($this->arrSlides as $slide):
            $slideParams = $slide["params"];
            $slideImage = $slideParams->get("image");

            $urlImageReized = UniteFunctionJoomla::getImageOutputUrl($slideImage, $this->width, $this->height, true);

            //set alt text
            $imageAltText = $slideParams->get("image_alt", "");
            if (empty($imageAltText)) {
                $info = pathinfo($slideImage);
                $imageAltText = UniteFunctions::getVal($info, "filename");
            }

            $link = $slideParams->get("link");

            //get boolean activate link
            $activateLink = $slideParams->get("activate_link");
            $activateLink = ($activateLink == "yes") ? true : false;

            $linkOpenIn = $slideParams->get("link_open_in", "new");

            $linkTarget = "";
            if ($linkOpenIn == "new")
                $linkTarget = " target='_blank'";

            $desc = $slideParams->get("description");

            //set title (reference to desc)
            $title = "";

            $hasCaption = $this->params->get("has_caption");
            
			//dmp($slideParams);exit();			
			
            if ($hasCaption != "false" && $this->isDescExists($desc)) {
                $descID = "nivo_desc_" . $slide["id"];
                $title = "title=\"#$descID\"";
            }
            ?>

            <?php if ($activateLink == true): ?>
                <a href="<?php echo $link ?>"<?php echo $linkTarget ?>><img src="<?php echo $urlImageReized ?>" alt="<?php echo $imageAltText ?>" <?php echo $title ?> ></a>
            <?php else: ?>
                <img src="<?php echo $urlImageReized ?>" alt="<?php echo $imageAltText ?>" <?php echo $title ?> width="<?php echo $this->width ?>" height="<?php echo $this->height ?>" >
            <?php endif; ?>

            <?php
        endforeach;
    }

    /**
     * put slides description html
     */
    private function putSlidesDesc() {
        $hasCaption = $this->params->get("has_caption");
        if ($hasCaption == "false")
            return(false);

        foreach ($this->arrSlides as $slide) {
            $slideParams = $slide["params"];
            $desc = $slideParams->get("description");
            $desc = trim($desc);

            if ($this->isDescExists($desc) == false)
                continue;

            $descID = "nivo_desc_" . $slide["id"];
            ?>
            <div id="<?php echo $descID ?>" class="nivo-html-caption"><span class="nivo-caption-inner"><?php echo $desc ?></span></div>
            <?php
        }
    }

    /**
     * 
     * put the javascript
     */
    private function putJs() {

        $params = $this->params;

        $strEffects = "random";
        $arrEffects = $params->get('effect', "");
        if (is_array($arrEffects) && !empty($arrEffects))
            $strEffects = implode(",", $arrEffects);

        $captionOpacity = $params->get("caption_back_alpha", "80");
        $captionOpacity = round($captionOpacity);
        $captionOpacity = $captionOpacity / 100;
        $captionOpacity = (string) $captionOpacity;
        $captionOpacity = str_replace(",", ".", $captionOpacity);

        //get conrolBG
        $controlBG = "false";
        $hasBullets = $this->params->get("has_bullets");
        if ($hasBullets != "false") {
            $setName = $this->params->get("bullets_set");
            $bulletsSet = HelperNivoPro::getBulletsSet($setName);
            if ($bulletsSet["is_bg"] == true)
                $controlBG = "true";
        }
        ?>
        <script type="text/javascript">

        <?php if ($this->noConflictMode == true): ?>
                jQuery.noConflict();
        <?php endif ?>

            jQuery('#<?php echo $this->sliderJSID ?>').show().nivoSlider({
                effect: '<?php echo $strEffects ?>',
                slices: <?php echo $params->get("slices", "15") ?>,
                boxCols: <?php echo $params->get("boxCols", "8") ?>,
                boxRows: <?php echo $params->get("boxRows", "4") ?>,
                animSpeed: <?php echo $params->get("animSpeed", "500") ?>,
                pauseTime: <?php echo $params->get("pauseTime", "3000") ?>,
                startSlide: <?php echo $params->get("startSlide", "0") ?>,
                keyboardNav: <?php echo $params->get("keyboardNav", "true") ?>,
                pauseOnHover: <?php echo $params->get("pauseOnHover", "true") ?>,
                manualAdvance: <?php echo $params->get("manualAdvance", "false") ?>,
                randomStart: <?php echo $params->get("randomStart", "false") ?>,
                controlNav: <?php echo $params->get("has_bullets", "true") ?>,
                controlBG: <?php echo $controlBG ?>,
                directionNav: <?php echo $params->get("has_arrows", "true") ?>,
                directionNavHide: <?php echo $params->get("directionNavHide", "true") ?>,
                captionOpacity: <?php echo $captionOpacity ?>,
                lastSlide: <?php echo $params->get("lastSlide", "function(){}") ?>,
                afterLoad: <?php echo $params->get("afterLoad", "function(){}") ?>,
                beforeChange: <?php echo $params->get("beforeChange", "function(){}") ?>,
                afterChange: <?php echo $params->get("afterChange", "function(){}") ?>,
                slideshowEnd: <?php echo $params->get("slideshowEnd", "function(){}") ?>,
                prevText: "",
                nextText: ""
            });
	    jQuery(document).ready(on_responsive)
	    jQuery(window).resize(on_responsive);
	    function on_responsive(){
	      cu_width =  jQuery('#<?php echo $this->sliderJSID ?>').width()
	      height = <?php echo $this->height ?>;
	      width = <?php echo $this->width ?>;
	      jQuery('#<?php echo $this->sliderJSID ?>').height(height/width * cu_width)
	      cu_height = height/width * cu_width;
	      jQuery('#<?php echo $this->sliderJSID ?> img').css('max-height', cu_height + 'px' );
	// hide bullet on screen
	<?php	if($this->params->get('hide_bullet_on',"")){ ?>
	   if((jQuery(window).width() < <?php echo $this->params->get('hide_bullet_on',""); ?>) || (screen.width < <?php echo $this->params->get('hide_bullet_on',""); ?> )){
	     jQuery('#<?php echo $this->sliderJSID ?> .nivo-controlNav').hide();
	   }else{
	     jQuery('#<?php echo $this->sliderJSID ?> .nivo-controlNav').show();
	   }
	<?php	} ?>
	// hide arrows on screen
	<?php	if($this->params->get('hide_arrows_on',"")){ ?>
	   if((jQuery(window).width() < <?php echo $this->params->get('hide_arrows_on',""); ?>) || (screen.width <<?php echo $this->params->get('hide_arrows_on',""); ?> )){
	     jQuery('#<?php echo $this->sliderJSID ?> .nivo-directionNav').hide();
	   }else{
	     jQuery('#<?php echo $this->sliderJSID ?> .nivo-directionNav').show();
	   }
	<?php	} ?>
	    }
        </script>
        <?php
    }

    /**
     * put html images preload
     */
    private function putPreloadImages() {

        //arrows hover 
        $hasArrows = $this->params->get("has_arrows");
        $arrPreload = array();
        if ($hasArrows != "false") {
            $arrowsSetName = $this->params->get("arrows_set");
            $arrowsSet = HelperNivoPro::getArrowSet($arrowsSetName);
            if ($arrowsSet["has_hover"] == true) {
                $arrPreload[] = $arrowsSet["url_left_hover"];
                $arrPreload[] = $arrowsSet["url_right_hover"];
            }
        }

        if (empty($arrPreload))
            return(false);
        ?>
        <div class="nivo-images-preload" style="opacity:0;position:absolute;">
        <?php
        foreach ($arrPreload as $imageUrl) {
            ?>
                <img src="<?php echo $imageUrl ?>" alt="preload image">
            <?php
        }
        ?>
        </div>
        <?php
    }

    /**
     * 
     * output slider body
     */
    private function putBody() {

        $title = $this->slider["title"];

        $params = $this->params;

        $style = "max-width:{$this->width}px;max-height:{$this->height}px;";

        //set wrapper style
        $wrapperStyle = "";

        //set wrapper position:
        $position = $params->get("position", "center");
        switch ($position) {
            case "center":
                $wrapperStyle .= "margin:0px auto;";
                break;
            case "left":
                $wrapperStyle .= "float:left;";
                break;
            case "right":
                $wrapperStyle .= "float:right;";
                break;
        }

        $wrapperStyle .= "max-width:{$this->width}px;";

        //set margin left / right
        if ($position == "left" || $position == "right") {
            $marginLeft = $params->get("margin_left", "0");
            $marginRight = $params->get("margin_right", "0");
            $wrapperStyle .= "margin-left:{$marginLeft}px;margin-right:{$marginRight}px;";
        }

        //set margin top/bottom
        $marginTop = $params->get("margin_top", "0");
        $marginBottom = $params->get("margin_bottom", "0");
        $wrapperStyle .= "margin-top:{$marginTop}px;margin-bottom:{$marginBottom}px;";

        $addClearBoth = $params->get("clear_both", "false");
        ?>
        <!--  Begin "Unite Nivo Slider Pro" title: <?php echo $title ?> -->
        <div class="nivo-slider-wrapper" style="<?php echo $wrapperStyle ?>">
            <div id="<?php echo $this->sliderJSID ?>" class="nivoSlider" style="<?php echo $style ?>">
        <?php $this->putSlides(); ?>
            </div>
        <?php $this->putPreloadImages(); ?>
        <?php $this->putSlidesDesc(); ?>			 	
        </div>
        <?php if ($addClearBoth == "true"): ?>
            <div style="clear:both"></div>
        <?php endif; ?>

        <!-- End Unite Nivo Slider Pro -->
        <?php
        $this->putJs();
    }

    /**
     * 
     * put border and shadow css
     */
    private function putCss_borderAndShadow() {
        $urlLoader = GlobalsNivoPro::$urlMediaAssets . "loaders/loading.gif";
        $hasShadow = $this->params->get("has_shadow");
        $hasBorder = $this->params->get("has_border");
        $marginBottom = $this->getMarginBottom();

        //set shadow style
        $styleShadow = "";
        if ($hasShadow != "false") {
            $shadowColor = $this->params->get("shadow_color");
            $styleShadow .= "-webkit-box-shadow: 0px 1px 5px 0px $shadowColor;";
            $styleShadow .= "-moz-box-shadow: 0px 1px 5px 0px $shadowColor;";
            $styleShadow .= "box-shadow: 0px 1px 5px 0px $shadowColor;";
        }

        //set border style
        $styleBorder = "";
        if ($hasBorder != "false") {
            $borderSize = $this->params->get("border_size");
            $borderColor = $this->params->get("border_color");
            $styleBorder = "border:{$borderSize}px solid $borderColor;";
        }
        ?>
        #<?php echo $this->sliderJSID ?>{
        position:relative;
        background:#fff url('<?php echo $urlLoader ?>') no-repeat 50% 50%;
        margin-bottom:<?php echo $marginBottom ?>px;
        <?php echo $styleShadow ?>
        <?php echo $styleBorder ?>
        }

        <?php
    }

    /**
     * 
     * put some css static values (not dynamic)
     */
    private function putCss_static() {
        ?>
        #<?php echo $this->sliderJSID ?> img {
        position:absolute;
        top:0px;
        left:0px;
        display:none;
        }

        #<?php echo $this->sliderJSID ?> .nivoSlider a {
        border:0;
        display:block;
        }			
        <?php
    }

    /**
     * 
     * get margin bottom for the slider, by the bullets position and height 
     */
    private function getMarginBottom() {
        $marginBottom = 0;

        $hasBullets = $this->params->get("has_bullets");
        if ($hasBullets == "false")
            return(0);

        $sliderHeight = $this->params->get("height");

        $setName = $this->params->get("bullets_set");
        $this->bulletsSet = HelperNivoPro::getBulletsSet($setName);
        $arrSet = $this->bulletsSet;
        $options = UniteFunctions::getVal($arrSet, "options");
        $isBG = $arrSet["is_bg"];

        $bulletHeight = UniteFunctions::getVal($options, "height");
        $bulletsY = $this->params->get("bullets_y");

        $bulletsEndY = $bulletsY + $bulletHeight;

        if ($isBG == true) {
            $bgHeight = UniteFunctions::getVal($options, "bg_height", 0);
            $bulletsEndY = $bulletsY + $bgHeight;
        }

        if ($bulletsEndY > $sliderHeight)
            $marginBottom = $bulletsEndY - $sliderHeight;

        return($marginBottom);
    }

    /**
     * 
     * put bullets
     */
    private function putCss_bullets() {

        $hasBullets = $this->params->get("has_bullets");
        if ($hasBullets == "false")
            return(false);

        $arrSet = $this->bulletsSet;

        $options = UniteFunctions::getVal($arrSet, "options");
        $reverseBullets = trim($this->params->get("reverse_bullets"));
        if (strtolower($reverseBullets) == "true") {
            $reverse = $arrSet["url_normal"];
            $arrSet["url_normal"] = $arrSet["url_active"];
            $arrSet["url_active"] = $reverse;
        }
        $bulletSpacing = $this->params->get("bullets_spacing");
        $bulletWidth = UniteFunctions::getVal($options, "width");
        $bulletHeight = UniteFunctions::getVal($options, "height");
        $bulletsY = $this->params->get("bullets_y");
        $bulletsLeft = $this->params->get("bullets_xleft");
        $bulletsRight = $this->params->get("bullets_xright");
        $bulletsAlign = $this->params->get("bullets_align");

        //set default Y
        if ($bulletsY == "" && $bulletsY !== 0) {
            $bulletsY = $this->height + 10;
        }


        $isBG = $arrSet["is_bg"];
        $mainBGStyle = "";
        $linkStyle = "";
        if ($isBG == true) {
            $imageLeft = $arrSet["url_bgleft"];
            $imageRight = $arrSet["url_bgright"];
            $imageRepeat = $arrSet["url_bgrepeat"];
            $leftWidth = UniteFunctions::getVal($options, "bg_left_width", 0);
            $rightWidth = UniteFunctions::getVal($options, "bg_right_width", 0);
            $paddingTop = UniteFunctions::getVal($options, "padding_top", 0);
            $bgHeight = UniteFunctions::getVal($options, "bg_height", 0);

            $linkStyle = "margin-top:" . $paddingTop . "px;";
            $mainBGStyle = "height:" . $bgHeight . "px;";
        }

        //count the center x position
        if ($bulletsAlign == "center") {
            //count bullets width:
            $totalWidth = $this->numSlides * (int) $bulletWidth;
            $totalWidth += ($this->numSlides - 1) * (int) $bulletSpacing;
            if ($isBG == true)
                $totalWidth += $leftWidth + $rightWidth;
            $sliderWidth = $this->params->get("width");
            $bulletsLeft = ($sliderWidth - $totalWidth) / 2;
            $bulletsLeft = round($bulletsLeft);
        }
// from version 3.0.8 for responsive
        $bulletsPosition = "left:" . $bulletsLeft / $this->width * 100  . "%;";

        if ($bulletsAlign == "right")
            $bulletsPosition = "right:" . $bulletsRight / $this->width * 100  . "%;";
        ?>

        #<?php echo $this->sliderJSID ?> .nivo-controlNav {
        position:absolute;
        <?php echo $bulletsPosition; ?>
        top:<?php echo $bulletsY / $this->height * 100  ?>%;
        <?php echo $mainBGStyle ?>
        z-index:10;
        }

        #<?php echo $this->sliderJSID ?> .nivo-controlNav a {				
        display:block;
        width:<?php echo $bulletWidth ?>px;
        height:<?php echo $bulletHeight ?>px;
        background:url(<?php echo $arrSet["url_normal"] ?>) no-repeat;
        border:0;
        margin-right:<?php echo $bulletSpacing ?>px;
        float:left;
        <?php echo $linkStyle ?>;
        }

        #<?php echo $this->sliderJSID ?> .nivo-controlNav a.active {
        background-image:url(<?php echo $arrSet["url_active"] ?>);
        }

        #<?php echo $this->sliderJSID ?> .nivo-controlNav a:last-child{
        margin-right:0px;
        }

        <?php
        //put bg styles
        if ($isBG == true):
            ?>

            #<?php echo $this->sliderJSID ?> .nivo-controlNav .nivo-controlNavLeft{
            background-image:url(<?php echo $imageLeft ?>);
            width:<?php echo $leftWidth ?>px;
            background-repeat:no-repeat;
            float:left;
            height:<?php echo $bgHeight ?>px;
            }

            #<?php echo $this->sliderJSID ?> .nivo-controlNav .nivo-controlNavMiddle{
            background-image:url(<?php echo $imageRepeat ?>);
            background-repeat:repeat-x;
            height:<?php echo $bgHeight ?>px;
            float:left;
            }

            #<?php echo $this->sliderJSID ?> .nivo-controlNav .nivo-controlNavRight{
            background-image:url(<?php echo $imageRight ?>);
            width:<?php echo $rightWidth ?>px;
            background-repeat:no-repeat;
            float:left;
            height:<?php echo $bgHeight ?>px;					
            }

            <?php
        endif;
    }

    /**
     * 
     * put arrows css
     */
    private function putCss_arrows() {

        $hasArrows = $this->params->get("has_arrows");


        $rightX = $this->params->get("arrow_right_x");
        $rightY = $this->params->get("arrow_right_y");

        $leftX = $this->params->get("arrow_left_x");
        $leftY = $this->params->get("arrow_left_y");


        if ($hasArrows == "false")
            return(false);

        $setName = $this->params->get("arrows_set");

        $arrowsSet = HelperNivoPro::getArrowSet($setName);
        $urlLeft = $arrowsSet["url_left"];
        $urlRight = $arrowsSet["url_right"];

        $options = UniteFunctions::getVal($arrowsSet, "options");
        $width = UniteFunctions::getVal($options, "width", "50");
        $height = UniteFunctions::getVal($options, "height", "50");

        $hasHover = $arrowsSet["has_hover"];
        if ($hasHover == true) {
            $urlLeftHover = $arrowsSet["url_left_hover"];
            $urlRightHover = $arrowsSet["url_right_hover"];
        }

        //if still no position
        if ($leftX == "" && $leftX !== 0) {
            $leftX = 10;
			$rightX = 10;
//          $rightX = $this->width - 10 - $width;
            $leftY = round($this->height / 2) - 20;
            $rightY = $leftY;
        }
        ?>

        #<?php echo $this->sliderJSID ?> .nivo-directionNav a {
        position:absolute;
        display:block;
        width:<?php echo $width ?>px;
        height:<?php echo $height ?>px;
        background-repeat:no-repeat;
        border:0;
        }

        #<?php echo $this->sliderJSID ?> a.nivo-prevNav {
        background-image:url(<?php echo $urlLeft ?>);
        top:<?php echo $leftY / $this->height * 100  ?>%;
        left:<?php echo $leftX / $this->width * 100  ?>%;
        }

        #<?php echo $this->sliderJSID ?> a.nivo-nextNav {				
        background-image:url(<?php echo $urlRight ?>);
        top:<?php echo $rightY / $this->height * 100 ?>%;
        right:<?php echo $rightX / $this->width * 100  ?>%;
        }

        <?php
        if ($hasHover == true):
            ?>
            #<?php echo $this->sliderJSID ?> a.nivo-prevNav:hover {
            background-image:url(<?php echo $urlLeftHover ?>);
            }

            #<?php echo $this->sliderJSID ?> a.nivo-nextNav:hover {
            background-image:url(<?php echo $urlRightHover ?>);
            }
            <?php
        endif;
    }

    /**
     * 
     * caption
     */
    private function putCss_caption() {

        $hasCaption = $this->params->get("has_caption");

        if ($hasCaption == "false") {
            ?>
            #<?php echo $this->sliderJSID ?> .nivo-caption {
            display:none;
            }
            <?php
            return(false);
        }

        $backAlpha = $this->params->get("caption_back_alpha");
        $backAlphaSmall = $backAlpha / 100;
        $stylePos = "bottom:0px;";
        if ($this->params->get("caption_position") == "top")
            $stylePos = "top:0px;";
        ?>
        #<?php echo $this->sliderJSID ?> .nivo-caption{
        left:0px;
        <?php echo $stylePos ?>
        background-color:<?php echo $this->params->get("caption_back_color") ?>;
        opacity:<?php echo $backAlphaSmall ?>;
        }

        #<?php echo $this->sliderJSID ?> .nivo-caption p {
        margin:0;
        padding:0px;
        text-align:<?php echo $this->params->get("text_align") ?>; 
        color:<?php echo $this->params->get("text_color") ?>;
        font-size:<?php echo $this->params->get("font_size") ?>px;
        }

        #<?php echo $this->sliderJSID ?> span.nivo-caption-inner{
        	display:block;
        	padding:<?php echo $this->params->get("text_padding") ?>px;
        }

        #<?php echo $this->sliderJSID ?> .nivo-caption a {
        text-decoration:underline;
        }

        #<?php echo $this->sliderJSID ?> .nivo-caption a:hover {
        text-decoration:underline;
        }

        <?php
    }

    /**
     * 
     * put css
     */
    private function putCss() {
        header("content-type:text/css");

        $this->putCss_borderAndShadow();
        $this->putCss_static();
        $this->putCss_bullets();
        $this->putCss_arrows();
        $this->putCss_caption();
    }

    /**
     * 
     * set the js to load from the body
     */
    public function setJsInBody() {
        $this->putJSInBody = true;
    }

    /**
     * 
     * disable the noconflict mode
     */
    public function enableNoConflict() {
        $this->noConflictMode = true;
    }

    /**
     * 
     * output the slider
     * @param $sliderID
     */
    public function outputSlider($sliderID) {
        try {
            $this->initSlider($sliderID);
            $this->initMainOutput();
            $this->includeClientFiles();
            $this->putBody();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->outputErrorMessage($message);
        }
    }

    /**
     * 
     * output the css
     */
    public function outputCss($sliderID, $sliderJSID) {

        try {
            $this->initSlider($sliderID);
            $this->initCSSOutput();
            $this->sliderJSID = $sliderJSID;
            $this->putCss();
        } catch (Exception $e) {
            $message = $e->getMessage();
            UniteFunctions::errorHtmlOutput($message);
        }
    }

}
?>