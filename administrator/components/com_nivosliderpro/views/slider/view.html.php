<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class NivoSliderProViewSlider extends JMasterViewNivoRev {

    protected $form;
    protected $item;
    protected $state;
    protected $style;
    protected $styleArrowLeft;
    protected $styleArrowRight;
    protected $urlArrowLeft;
    protected $urlArrowRight;
    protected $bulletsHtml;
    protected $styleBullets;
    protected $classBullets;
    protected $styleCaptions;
    protected $styleCaptionsText;
    protected $linkEditSlides;
    protected $isNew = true;
    protected $sap_counter = 0;

    /**
     * 
     * get "visual" fieldset value
     */
    public function getVisualValue($field, $default = "") {

        $value = UniteFunctionJoomla::getFormFieldValue($this->form, $field, "visual");
        if ($value === "")
            $value = $default;

        return($value);
    }

    /**
     * 
     * set "visual" form field
     */
    public function setVisualValue($field, $value) {

        $this->form->setValue($field, "visual", $value);
    }

    /**
     * 
     * hide field in visual panel
     */
    public function hideVisualField($field) {
        UniteFunctionJoomla::hideFormField($this->form, $field, "visual");
    }

    /**
     * get visual tab slider style element
     */
    private function setVisualStyle() {
        $width = $this->getVisualValue("width");
        $height = $this->getVisualValue("height");
        $hasBorder = $this->getVisualValue("has_border");
        $hasShadow = $this->getVisualValue("has_shadow");

        //get fixed background url
        $urlBack = GlobalsNivoPro::$urlAssets . "images/slide_image.jpg";

        //set border style
        $styleBorder = "";
        if ($hasBorder == true) {
            $borderSize = $this->getVisualValue("border_size");
            $borderColor = $this->getVisualValue("border_color");
            $styleBorder = "border:{$borderSize}px solid $borderColor;";
        }

        //set style:
        $style = "width:{$width}px;height:{$height}px;";
        $style .= "background-image:url('$urlBack');";
        $style .= "background-repeat:repeat;background-position:center center;";

        if ($hasBorder)
            $style .= $styleBorder;

        if ($hasShadow) {
            $shadowColor = $this->getVisualValue("shadow_color");
            $style .= "-webkit-box-shadow: 0px 1px 5px 0px $shadowColor;";
            $style .= "-moz-box-shadow: 0px 1px 5px 0px $shadowColor;";
            $style .= "box-shadow: 0px 1px 5px 0px $shadowColor;";
        }

        $this->style = $style;
    }

    /**
     * 
     * set bullets related variables
     */
    private function setVisualBullets() {
        $setName = $this->getVisualValue("bullets_set");
        $arrSet = HelperNivoPro::getBulletsSet($setName);

        //get spacing
        $spaceMiddle = $this->getVisualValue("bullets_spacing");
        $spaceMiddle = trim($spaceMiddle);
        $options = UniteFunctions::getVal($arrSet, "options");
        // get reverse option
        $options['reverse'] = $this->getVisualValue("reverse_bullets");
        if($options['reverse']){
            $reverse = $arrSet["url_active"];
            $arrSet["url_active"] =$arrSet['url_normal'] ;
            $arrSet['url_normal']=$reverse;
        }
        //set spacing from options		
        if ($spaceMiddle === "") {
            $spaceMiddle = UniteFunctions::getVal($options, "space_middle");

            $this->setVisualValue("bullets_spacing", $spaceMiddle);
        } else { //set visual spacing value
            if (is_numeric($spaceMiddle)) {
                $options["space_middle"] = $spaceMiddle;
                $arrSet["options"] = $options;
            }
        }

        //set bullets html
        $this->bulletsHtml = HelperNivoPro::getBulletsHtml($arrSet);

        //set visible fields
        $align = $this->getVisualValue("bullets_align");
        switch ($align) {
            case "left":
                $this->hideVisualField("bullets_xright");
                break;
            case "right":
                $this->hideVisualField("bullets_xleft");
                break;
            case "center":
                $this->hideVisualField("bullets_xleft");
                $this->hideVisualField("bullets_xright");
                break;
        }

        $showBullets = $this->getVisualValue("has_bullets");

        //set bullets style attribute:
        $style = "";
        $sliderWidth = (int) $this->getVisualValue("width");
        $sliderHeight = (int) $this->getVisualValue("height");

        //set y position
        $bulletsY = $this->getVisualValue("bullets_y");
        if ($bulletsY === "") {
            $bulletsY = $sliderHeight + 18;
            $this->classBullets = " invisible";
        }

        $style .= "top:{$bulletsY}px;";

        //set x position
        $bulletsX = $this->getVisualValue("bullets_xleft");
        if ($bulletsX !== "" && is_numeric($bulletsX)) {
            $style .= "left:{$bulletsX}px;";
        }

        //set visibility
        $showBullets = $this->getVisualValue("has_bullets");

        if ($showBullets == false)
            $style .= "display:none;";

        $this->styleBullets = $style;
    }

    /**
     * 
     * set captions style
     */
    private function setVisualCaptionsStyle() {
        $style = "";

        //set back color
        $backColor = $this->getVisualValue("caption_back_color");
        $style = "background-color:$backColor;";

        //set back transparency
        $backAlpha = $this->getVisualValue("caption_back_alpha");
        $alpha1 = (double) ($backAlpha / 100);
        $alpha2 = (double) ($backAlpha / 10);

        $style .= "opacity:$alpha1;filter:alpha(opacity=$alpha2);";

        $position = $this->getVisualValue("caption_position");
        if ($position == "bottom")
            $style .= "bottom:0px;";
        else
            $style .= "top:0px;";

        //set caption visibility
        $showCaption = $this->getVisualValue("has_caption");
        if ($showCaption == false)
            $style .= "display:none;";

        $this->styleCaptions = $style;
    }

    /**
     * 
     * set captions text style
     */
    private function setVisualCaptionsTextStyle() {
        $style = "";

        $textColor = $this->getVisualValue("text_color");
        $textAlign = $this->getVisualValue("text_align");
        $textPadding = $this->getVisualValue("text_padding");
        $fontSize = $this->getVisualValue("font_size");

        $style .= "color:$textColor;text-align:$textAlign;padding:{$textPadding}px;font-size:{$fontSize}px;";

        $this->styleCaptionsText = $style;
    }

    /**
     * 
     * get arrows style
     */
    private function setVisualArrowsStyle() {

        $arrowSetName = $this->getVisualValue("arrows_set");
        $arrowSet = HelperNivoPro::getArrowSet($arrowSetName);

        $sliderHeight = $this->getVisualValue("height");
        $defaultArrowsPosY = round($sliderHeight / 2) - 20;

        $arrowLeftX = $this->getVisualValue("arrow_left_x", "10");
        $arrowLeftY = $this->getVisualValue("arrow_left_y", $defaultArrowsPosY);
        $arrowRightX = $this->getVisualValue("arrow_right_x", "none");
        $arrowRightY = $this->getVisualValue("arrow_right_y", $defaultArrowsPosY);

        $styleArrowLeft = "left:{$arrowLeftX}px;top:{$arrowLeftY}px;";

        if ($arrowRightX == "none") //set "right" value (on init)
            $styleArrowRight = "right:{$arrowLeftX}px;top:{$arrowRightY}px;";
        else  //set "left" value
            $styleArrowRight = "right:{$arrowRightX}px;top:{$arrowRightY}px;";

        //set visibility:
        $showArrows = $this->getVisualValue("has_arrows");

        if ($showArrows == false) {
            $styleArrowLeft .= "display:none;";
            $styleArrowRight .= "display:none;";
        }

        //set the arrows for html
        $this->urlArrowLeft = $arrowSet["url_left"];
        $this->urlArrowRight = $arrowSet["url_right"];


        $this->styleArrowLeft = $styleArrowLeft;
        $this->styleArrowRight = $styleArrowRight;
    }

    /**
     * 
     * put visual vield html
     */
    public function putVisualField($name) {

        //set arrows style
        $showArrows = $this->getVisualValue("has_arrows");
        $arrowsStyle = "";
        if ($showArrows == false)
            $arrowsStyle = "style='display:none;'";

        //set bullets style
        $showBullets = $this->getVisualValue("has_bullets");
        $bulletsStyle = "";
        if ($showBullets == false)
            $bulletsStyle = "style='display:none;'";

        $captionsStyle = "";

        switch ($name) {
            case "section_arrows_start":
                echo "<span id='section_arrows' $arrowsStyle>";
                break;
            case "section_bullets_start":
                echo "<span id='section_bullets' $bulletsStyle>";
                break;
            case "section_captions_start":
                echo "<span id='section_captions' $captionsStyle>";
                break;
            case "section_captiontext_start":
                echo "<span id='section_captiontext' $captionsStyle>";
                break;
            case "section_end":
                echo "</span>";
                break;
            case "section_arrows_end":
                break;
            case "sap":
                $this->sap_counter++;

                echo "<span id='visual_sap_{$this->sap_counter}' class='sap'>|</a></span>";
                break;
            default:
                echo $this->form->getLabel($name, 'visual');
                echo $this->form->getInput($name, 'visual');
                break;
        }
    }

    /**
     * 
     * put array of visual fields
     */
    public function putVisualFields($category) {
        switch ($category) {
            case "general":
                $arrFieldNames = array(
                    "width",
                    "height",
                );

                break;
            case "border":

                $arrFieldNames = array(
                    "has_border",
                    "border_size",
                    "border_color",
                );

                break;
            case "shadow":

                $arrFieldNames = array(
                    "has_shadow",
                    "shadow_color"
                );

                break;
            case "arrows":

                $arrFieldNames = array(
                    "has_arrows",
                    "section_arrows_start",
                    "arrows_set",
                    "sap",
                    "spacer_left_arrow",
                    "arrow_left_x",
                    "arrow_left_y",
                    "sap",
                    "spacer_right_arrow",
                    "arrow_right_x",
                    "arrow_right_y",
                    "sap",
                    "arrows_free_drag",
                    "arrows_gocenter",
                    "section_end",
		    "hide_arrows_on"
                );

                break;
            case "bullets":

                $arrFieldNames = array(
                    "has_bullets",
                    "section_bullets_start",
                    "bullets_set",
                    "sap",
                    "bullets_align",
                    "bullets_y",
                    "bullets_xleft",
                    "bullets_xright",
                    "sap",
                    "bullets_spacing",
                    'reverse_bullets',
                    "section_end"
                    
                );

                break;
            case "captions":
                $arrFieldNames = array(
                    "has_caption",
                    "section_captions_start",
                    "caption_back_color",
                    "caption_back_alpha",
                    "caption_position",
                    "section_end"
                );

                break;
            case "captiontext":
                $arrFieldNames = array(
                    "section_captiontext_start",
                    "text_color",
                    "text_align",
                    "text_padding",
                    "font_size",
                    "section_end"
                );
                break;
            default:
                UniteFunctions::throwError("putVisualFields: Wrong category: $category");
                break;
        }

        foreach ($arrFieldNames as $fieldName)
            $this->putVisualField($fieldName);
    }

    /**
     * 
     * add toolbars
     */
    protected function addToolbar() {

        $title = JText::_('COM_NIVOSLIDERPRO') . " - ";
        if ($this->isNew)
            $title .= '<small>[ ' . JText::_('COM_NIVOSLIDERPRO_NEW') . ' ]</small>';
        else
            $title .= $this->item->title . " <small>[" . JText::_("COM_NIVOSLIDERPRO_EDIT_SETTINGS") . "]</small>";

        JToolBarHelper::title($title, 'generic.png');

        if ($this->isNew) {
            // For new records, check the create permission.
            JToolBarHelper::apply('slider.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('slider.save', 'JTOOLBAR_SAVE');
            JToolBarHelper::custom('slider.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            JToolBarHelper::cancel('slider.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::apply('slider.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('slider.save', 'JTOOLBAR_SAVE');
            //JToolBarHelper::custom('slider.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            JToolBarHelper::cancel('slider.cancel', 'JTOOLBAR_CANCEL');
        }
    }

    /**
     * the main disply function
     */
    public function display($tpl = null) {
        // Initialiase variables.
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        $this->isNew = ($this->item->id == 0);


        if ($this->_layout == "default" || $this->_layout == "edit") {

            if ($this->isNew == false) {
                $this->linkEditSlides = HelperNivoPro::getViewUrl_Items($this->item->id);
            }

            //set visual fields
            $this->setVisualStyle();
            $this->setVisualArrowsStyle();
            $this->setVisualBullets();
            $this->setVisualCaptionsStyle();
            $this->setVisualCaptionsTextStyle();
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

}
