<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

// load TCPDF library (if not loaded somewhere in Joomla! yet)
if (!class_exists('TCPDF', false)) { //false: no autoload!
	require_once (dirname(__FILE__) . '/../libraries/tcpdf/config/lang/eng.php');
	require_once (dirname(__FILE__) . '/../libraries/tcpdf/tcpdf.php');
	require_once (dirname(__FILE__) . '/../libraries/tcpdf/config/tcpdf_config.php');
	define('VMINVOICE_OWN_TCPDF',TRUE);
}

// load HTML class
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'invoicehtmlparent.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'invoicehtml.php');

/**
 * TCPDF class extension to generate Invoices / Delivery Notes.
 * 
 * @author miun
 * @author pama
 */
class InvoiceTCPDF extends TCPDF
{
	var $params;
    var $html;
    var $bottomMargins = array(); //for computed bottom margin height
    var $lastPage = false;
    var $tidy_options = array ( //we need to specify utf-8 encoding
				'clean' => 1,
				'drop-empty-paras' => 0,
				'drop-proprietary-attributes' => 1,
				'fix-backslash' => 1,
				'hide-comments' => 1,
				'join-styles' => 1,
				'lower-literals' => 1,
				'merge-divs' => 1,
				'merge-spans' => 1,
				'output-xhtml' => 1,
				'word-2000' => 1,
				'wrap' => 0,
				'output-bom' => 0,
				'char-encoding' => 'utf8',
				'input-encoding' => 'utf8',
				'output-encoding' => 'utf8',
        		'preserve-entities' => 1
			);
    
    
	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
	
		$this->params = NbordersHelper::getParams();
		 
		JPluginHelper::importPlugin('vminvoice');
		$this->dispatcher = JDispatcher::getInstance();
		
		//allow plugin to change function arguments
        $arguments = array(&$orientation, &$unit, &$format, &$unicode, &$encoding, &$diskcache, &$pdfa);
        $this->dispatcher->trigger('onPDFBeforeInit', array(&$this, $this->params, &$arguments));

        //construct!
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		
	    //determine if server has Tidy and in proper version (must have preserve-entities option)
        $this->canTidy=false;
        if ($this->params->get('use_tidy',1)==1) {
        	if (function_exists('tidy_get_release')){
				if (strtotime(tidy_get_release()>strtotime('2007-08-13')))
					$this->canTidy=true;
			}
        }
        
        //check if we have own and proper version of TCPDF loaded
        if ($this->params->get('debug',0)){
	        if (!defined('VMINVOICE_OWN_TCPDF')){ //display warning, if is not used TCPDF from VM Invoice directory
				$version = method_exists($this,'getTCPDFVersion') ? $this->getTCPDFVersion() : (isset($this->tcpdf_version) ? $this->tcpdf_version : false);
				echo '-----------------------------------------------------------<br>
					  WARNING: Different version '.($version ? '('.$version.')' : '').' of TCPDF from some other Joomla! extension is loaded. 
					  		<br>This can be source of compatibility problems.
					  		<br>TCPDF coming with VM Invoice is usually newest with some bugs fixed.
					  		<br>Uploaded fonts may also not be working, because foreign TCPDF uses different config file and thus directory for searching fonts.
					  		<br>To get your uploaded fonts working, copy files from JOOMLA_ROOT/administrator/components/com_netbasevm_extend/libraries/tcpdf/fonts
					  		into fonts directory used by other TCPDF class. 
					  		<br>If it is Joomla! native TCPDF, directory is JOOMLA_ROOT/libraries/tcpdf/fonts.
					  -----------------------------------------------------------<br>';
			}	
        }
        
        $this->SetCreator(PDF_CREATOR);
        
        // disable header and footer
        $this->setPrintHeader(true);
        $this->setPrintFooter(true);
        
        // header and footer margins
        $this->setHeaderMargin(20);
        $this->setFooterMargin(10);
        
        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $this->SetMargins($this->params->get('margin_left', 15), PDF_MARGIN_TOP, $this->params->get('margin_right', 15));
        //set auto page breaks
        $this->SetAutoPageBreak(false);
        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        $this->setFontSubsetting($this->params->get('font_subset', 1) ? true : false);
        
        $this->setHeaderFont((array)$this->params->get('font', 'arial'));
        $this->setFooterFont((array)$this->params->get('font', 'arial'));
        $this->SetFont($this->params->get('font', 'arial'), '', $this->params->get('font_size', 10));
        
        //trigger after init event (mainly place to change/add some tcpdf settings)
        $this->dispatcher->trigger('onPDFAfterInit', array(&$this, $this->params));
	}

	public function saveInvoicePDF($filename)
	{
		$this->Output($filename, 'F');
		return true; //TODO: check
	}
	
	/**
	 * Main method to add invoice page(s)
	 */
	public function addInvoicePage($orderID, $deliveryNote)
	{
        $this->SetAuthor($this->invoiceAuthor);
        $this->SetTitle($this->invoiceTitle);
        $this->SetSubject($this->invoiceSubject);
        $this->SetKeywords($this->invoiceKeywords);
        
        $language = NbordersHelper::getInvoiceLanguage($orderID);
		$this->html = new InvoiceHTML($orderID, $deliveryNote, $language);
		
		//print_r($this->html);die;
		//$cosi = $this->getHTMLUnitToUnits('1cm', 0, $this->imgscale * $this->k, true);

		$this->html->scaleCmToPDF = $this->imgscale * $this->k;
				
        $code = $this->html->getHTML('body',$this->getPage(), $this->getPageNumGroupAlias(),$this->getPageGroupAlias());
		//echo $code;die;
		if ($this->canTidy)
	        $code = $this->fixHTMLCode($code,'','',$this->tidy_options);
 
        if ($code===false){
        	echo "Could not get invoice body";
			exit;
		}
        	
        $code = str_replace('&nbsp;', '', $code);
        //echo $code;die;
		$result = $this->dispatcher->trigger('onPDFBeforeCreate', array(&$this, &$this->html, $orderID, $deliveryNote, &$code));
		//echo $code;die;
		if(in_array(false, $result, true)) //some plugin returned false - means no other operations with this object (for example he could write to pdf object on its own)
			return ;
		
        $this->outputDebug('body',$code);

        if ($this->params->get('debug',0)==2) //not trigger tcpdf
        	return ;
        
        $this->startPageGroup();
        $this->lastPage=false;
        $this->AddPage();     
        $this->lastPage=false;
        $this->startingPage = $this->getPage(); //store invoice starting page
        $this->SetAutoPageBreak(TRUE, $this->getBottomMargin() + 10); //set auto page break to (!not last page) footer length + whote space between footer and content
        //write content!
		//echo $code;die;
        $this->writeHTML($code, true, false, false, false, '');

        //special behavior of footer: last footer can be bigger than footers before
        //so in that case we must check in advance if it will not interfer with text
        //an din that case, we must add another page to fit all
        
        //1. get space left on bottom after content writing
        $spaceLeft = $this->getPageHeight() - $this->GetY();
        
        //2. get bottom margin ON LAST PAGE
        $this->lastPage=true; 
        $lastPageBottomMargin = $this->getBottomMargin();
        $this->lastPage=false;
        
        //3. if no enough room for last page footer, we must end page normally and add another last.
        if ($spaceLeft<$lastPageBottomMargin-2){ //tolerance :D
        	$this->lastPage=false;
        	$this->endPage();
        	$this->startPage();
        }

        $this->lastPage=true;
        $this->endPage(); //real end
	}
	
	/**
	 * Output HTML code for debug.
	 * 
	 * @param string $type
	 * @param string $code
	 */
    public function outputDebug($type,$code)
    {	
    	if ($this->params->get('debug',0)){ //output for debug mode
			if (!isset($this->debugged[$type])){
        		echo '---------------------------------------<br>'.ucfirst($type).
        		' Layout:<br>---------------------------------------<br> '.str_replace('</body>','</body >',$code); //because joomla debug plugin
        		
        		echo '---------------------------------------<br>'.ucfirst($type).
        		' HTML Code:<br>---------------------------------------<br> '.nl2br(htmlspecialchars($code));
        		
        		echo '<br>';
        		
        		$this->debugged[$type]=true;
			}
		}
	}
	
    // Load table data from file
    public function LoadData ($file)
    {
        // Read file lines       
        $lines = file($file);
        $data = array();
        
        foreach ($lines as $line) {
            $data[] = explode(';', chop($line));
        }
        
        return $data;
    }

    public function Header()
    {
		$result = $this->dispatcher->trigger('onPDFBeforeHeader', array(&$this));
		if(in_array(false, $result, true)) //some plugin returned false - means no other operations with this object (for example he could write to pdf object on its own)
			return ;
		
    	$imageInfo=false;
        if ($bgImage = $this->params->get('background_image', null)){ 
        	$imageInfo = getimagesize($this->html->parseImagePath($bgImage, 'rel_full_path')); //getimagesize must get server path
        	$bgImage = $this->html->parseImagePath($bgImage); 
        }

        if ($imageInfo) //add background image
        {
        	//http://www.tcpdf.org/examples/example_051.phps
	    	// get the current page break margin
	        $bMargin = $this->getBreakMargin();
	        // get current auto-page-break mode
	        $auto_page_break = $this->AutoPageBreak;
	        // disable auto-page-break
	        $this->SetAutoPageBreak(false, 0);
	        
	        // set background image
	        
        	//get background position
	        $pos = $this->params->get('background_image_pos', 'TC');
	       	$posX = $this->params->get('background_image_pos_x', 0);
	        $posY = $this->params->get('background_image_pos_y', 0);
	        $stretch = $this->params->get('background_stretch', 0); 
	        
	        if ($stretch==1){ //wheater stretch image to whole page
	        	
	        	$imageWidth = $this->getPageWidth()  - $posX;
	        	$imageHeight = ($imageInfo[1]/$imageInfo[0])*$imageWidth;
	        	
	        	if ($imageHeight>$this->getPageHeight()){
	        		$imageHeight = $this->getPageHeight()  - $posY;
	        		$imageWidth = ($imageInfo[0]/$imageInfo[1])*$imageHeight;
	        	}
	        }
	        else{
		        $imageWidth = $imageInfo[0]/6; //note that sizes are in cm. ratio to not see ugly pixelized image.
		        $imageHeight = $imageInfo[1]/6;
	        }
	
	        if (!empty($posY)) //if y pos overriden
	        	$y = $posY;
	        else{ //else compute from dropdown value
		        if ($pos == 'TL' OR $pos == 'TC' OR $pos == 'TR') $y=0;
		        elseif ($pos == 'ML' OR $pos == 'MC' OR $pos == 'MR') $y=($this->getPageHeight()-$imageHeight)/2;
		        elseif ($pos == 'BL' OR $pos == 'BC' OR $pos == 'BR') $y=$this->getPageHeight()-$imageHeight;
	        }

	        if (!empty($posX)) { //if x position is overriden
	        	$x=$posX;
	        	$palign='';
	        } else { //else use dropdown value
	        	$x='';
	        	if ($pos == 'TL' OR $pos == 'ML' OR $pos == 'BL') {$x = 0; $palign=''; /*$palign='L';*/}
	        	elseif ($pos == 'TC' OR $pos == 'MC' OR $pos == 'BC') $palign='C'; //can stay
	        	elseif ($pos == 'TR' OR $pos == 'MR' OR $pos == 'BR') {$x = $this->getPageWidth() - $imageWidth; $palign='';/*$palign='R';*/}
	        }
			//http://www.tcpdf.org/doc/classTCPDF.html#a714c2bee7d6b39d4d6d304540c761352
           	$this->Image($bgImage, $x, $y, $imageWidth, $imageHeight, '', '', '', true, 300, $palign, false, false, 0, false, false, false);
	        // restore auto-page-break status
	        $this->SetAutoPageBreak($auto_page_break, $bMargin);
	        // set the starting point for the page content
	        $this->setPageMark();
       }
       $this->SetAutoPageBreak(TRUE, $this->getBottomMargin());
        
       // set font
       $this->SetFont($this->params->get('font', 'arial'), '', $this->params->get('font_size', 10));
       
       //set top margin
       $this->SetY($this->params->get('margin_top', 10)); 
       
       // write header
       $code = $this->html->getHTML('header', $this->getPage(), $this->getPageNumGroupAlias(), $this->getPageGroupAlias());
       if ($this->canTidy)
			$code = $this->fixHTMLCode($code,'','',$this->tidy_options);

	   $this->outputDebug('header',$code);
		if ($this->params->get('debug',0)==2) //not trigger tcpdf
        	return ;
		 
       //$this->writeHTML($code, false, false, true, false, ''); 
		$this->writeHTML($code, false, false, true, false, 'L'); 
       //set top margin based on current header ending
       $this->SetTopMargin ($this->GetY()+5);

       //trigger plugin
       $this->dispatcher->trigger('onPDFAfterHeader', array(&$this));
    } 

    public function Footer()
    {
    	$result = $this->dispatcher->trigger('onPDFBeforeFooter', array(&$this));
		if(in_array(false, $result, true)) //some plugin returned false - means no other operations with this object (for example he could write to pdf object on its own)
			return ;
		
        // set font
        $this->SetFont($this->params->get('font', 'arial'), '', $this->params->get('font_size', 10));
       
        // set distance from bottom
        $this->SetY(-1 * $this->getBottomMargin());

        // http://sourceforge.net/projects/tcpdf/forums/forum/435311/topic/3905275
        // we cannot know total number of pages before whole document is created.
        // only thing we can know it is THIS footer is on last page ($this->lastPage is set to true from NbordersHelper->addPage())
        // because every other footer() is called automatically when new page is created.
        
        // also we can know it is footer of document with only one page (useful if show pagination setting only when more pages)
        $onlyOnePage = ($this->lastPage && ($this->getPage()-$this->startingPage ==0)) ? true : false;
         
        // write html code
        $code = $this->html->getHTML('footer', $this->getPage(), $this->getPageNumGroupAlias(), $this->getPageGroupAlias(), $this->lastPage, $onlyOnePage);

        if ($this->canTidy)
        	$code = $this->fixHTMLCode($code,'','',$this->tidy_options);
	
        $this->outputDebug('footer',$code);
        if ($this->params->get('debug',0)==2) //not trigger tcpdf
        	return ;
        
        //$this->writeHTML($code, true, false, false, false, '');
        $this->writeHTML($code, false, false, true, false, 'L');

		$this->dispatcher->trigger('onPDFAfterFooter', array(&$this));
    }
    
	/**
	 * Get margin from bottom (footer height)
	 * 
	 * From http://www.tcpdf.org/doc/classTCPDF.html#ad68e86a862fe437a8ac1728cecaaa2e9
	 * "Generally, if you want to know the exact height for a block of content you can use the following alternative technique:"
	 * 
	 * BUT UPATE 1.2.2013! there seems to be error with startTransaction which there are here caused nobr="true" tags not worning. 
	 * So use just simple clone by create temporary object. (not delete it for all cases)
	 */
    function getBottomMargin()
    {
    	$page = $this->getPage();
    	    	
    	if (!isset($this->bottomMargins[$page][$this->lastPage])) //cache not to get same margin twice again
    	{
	        $onlyOnePage = ($this->lastPage && ($this->getPage()-$this->startingPage ==0)) ? true : false;
	        $code = $this->html->getHTML('footer',$this->getPage(), $this->getPageNumGroupAlias(),$this->getPageGroupAlias(),$this->lastPage,$onlyOnePage);
			if ($this->canTidy)
	        	$code = $this->fixHTMLCode($code,'','',$this->tidy_options);
			
	 
	        $newObj = clone $this; //see fnc comment..
	        $newObj->SetFont($newObj->params->get('font', 'arial'), '', $newObj->params->get('font_size', 10)); // set font
	        
	        if ($newObj->getNumPages()>1)
	        	$newObj->deletePage($newObj->getPage()); 
	        	
	        //$newObj->AddPage(); //shuts down tcpdf..

	        $start_page = $newObj->getPage();
	        /*
	        if (!$start_page){ //if no pages added yet
	        	$newObj->AddPage();
	        	$start_page = $newObj->getPage();}
	        	*/
	        $newObj->setY(20); //it must be that way, or on last page is height counted differently
	        
	        $start_y = $newObj->GetY();   // store starting values         
	        //$newObj->writeHTML($code, true, false, false, false, ''); // call printing functions with your parameters
			$newObj->writeHTML($code, false, false, true, false, 'L'); 
			$end_y = $newObj->GetY();  // get the new Y 
			$end_page = $newObj->getPage(); 
			$height = 0; 
			if ($end_page == $start_page) 
				{ $height += $end_y - $start_y; }
			else { 
				for ($page=$start_page; $page <= $end_page; ++$page) { 
					$newObj->setPage($page); 
					if ($page == $start_page) { $height += $newObj->h - $start_y - $newObj->bMargin; } // first page 
					elseif ($page == $end_page) { $height += $end_y - $newObj->tMargin; }  // last page 
					else { $height += $newObj->h - $newObj->tMargin - $newObj->bMargin; } 
				} 
			} 

			$height += $this->params->get('margin_bottom', 10); //add bottom margin from config
			
			$this->bottomMargins[$page][$this->lastPage]=$height;
    	}
    	
    	//echo "<br><br>height:".$this->bottomMargins[$page][$this->lastPage]."starty $start_y endy $end_y startpage $start_page endpage $end_page";
    	
		return $this->bottomMargins[$page][$this->lastPage];
    }

}
?>