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
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Articles list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since	1.6
 */
class NetBaseVm_ExtendControllerTemplateInvoice extends JControllerLegacy
{
	function __construct ($config = array())
    {
        parent::__construct($config);

    }

    function display($cachable = false, $urlparams = false)
    {
       	JRequest::setVar('view', 'templateinvoice');
        parent::display($cachable, $urlparams);
    }
	
	// create file pdf
	function createPDF()
	{
		if (!class_exists('TCPDF', false)) { //false: no autoload!
			require_once(JPATH_ADMINISTRATOR.DS. 'components' . DS . 'com_netbasevm_extend' . DS.'libraries'.DS .'tcpdf'.DS.'config'.DS.'lang'.DS.'eng.php');
			require_once(JPATH_ADMINISTRATOR.DS. 'components' . DS . 'com_netbasevm_extend' . DS.'libraries'.DS .'tcpdf'.DS.'config'.DS.'tcpdf_config_netbase.php');
			
			require_once(JPATH_ADMINISTRATOR.DS. 'components' . DS . 'com_netbasevm_extend' . DS.'libraries'.DS .'tcpdf'.DS.'tcpdf.php');
		}
		
		jimport('joomla.filesystem.file');
		// delete file exist
		/*$path_pdf = JPATH_ROOT.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'docs'.DS.'example_006_jquery.pdf';
		//echo $path_pdf;die;
		if ( JFile::exists($path_pdf) ) {
			echo 'test';die;
			if(JFile::delete($path_pdf))
			{
				echo 'File deleted and doing create again..';die();
			}
		}
		else
		{
			echo 'Not delete files good.';
			//exit();
		}
		*/
		// get values submit 
		$template=$_POST['template'];
		
		$items=$_POST['items'];
		
		
		// write file template_general.html
		
		$template_json=json_encode($template);
		$path = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_general.json';
		//echo $path;die;
		if ( !JFile::exists($path) ) {
			echo "File not exist !";
				exit();
		}
		else
			JFile::write($path, $template_json);
		
		// write file template_product.html
		$items_json=json_encode($items);
		$path_product = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_product.json';
		if ( !JFile::exists($path_product) ) {
				echo "File not exist !";
				exit();
		}
		else
			JFile::write($path_product, $items_json);
		
		
		//print_r($template);die;
		//print_r($items);die;
		
		//$txt=$_POST['texthtml'];
		//$txt_product=$_POST['textproduct'];
		
		// add css
$style = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>
   
   ul,li
   {
	   padding:0px;
	   margin:0px;
	   list-style-type: none;
   }
   p
   {
	   color:red;
	}
</style>
EOF;
		/*
		$html=str_replace('{products}',$txt_product,$txt);
		// replace {break} = <br/>
		$html=str_replace('{space}','&nbsp;',$html);
		// replace {break} = <br/>
		$html=str_replace('{break}','<br/>',$html);
		
		$html=$style.$html;
		*/
		
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Netbase');
		$pdf->SetTitle('Template invoices');
		$pdf->SetSubject('TCPDF Tutorial');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
		
		// set header and footer fonts
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// remove default header
		$pdf->setPrintHeader(false);
		// remove default footer
		//$pdf->setPrintFooter(false);
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('helvetica', '', 10);
		
		// add a page
		$pdf->AddPage();
		
		/*
		* Examples about create html, multicell
		*
		*/
		/*
		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		
		// create some HTML content
		//$html = $html;
		
		// output the HTML content
		//$pdf->writeHTML($html, true, false, true, false, '');
		
		// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
		
		// set some text for example
		$txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
		
		// Multicell test
		// set cell padding
		$pdf->setCellPaddings(1, 1, 1, 1);
		// set cell margins
		$pdf->setCellMargins(1, 1, 1, 1);
		// set color for background
		$pdf->SetFillColor(255, 255, 127);
		//$border=array('B'=>array('width'=>1,'color'=>'#000'));
		$pdf->MultiCell(55, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
		
	
		// set cell padding
		$pdf->setCellPaddings(10, 10, 10, 10);
		// set cell margins
		$pdf->setCellMargins(1, 1, 1, 1);
		// set color for background
		$pdf->SetFillColor(255, 255, 127);
		$text='<b>test</b>';
		
		$pdf->MultiCell(55, 5, '[RIGHT] '.$text, 0, 'R', 1, 0, '', '', true,0,true);
		$pdf->MultiCell(55, 5, '[CENTER] '.$txt, 1, 'C', 0, 0, '', '', true);
		$pdf->Ln();
		$pdf->MultiCell(55, 5, '[JUSTIFY] '.$txt."\n", 1, 'J', 0, 0, '' ,'', true);
		// set font size
		$pdf->SetFont('helvetica', '', 20);
		
		$pdf->MultiCell(55, 5, '[DEFAULT] '.$txt, 1, 'L', 0, 0, '', '', true);
		
		$pdf->Ln();
		$pdf->Ln(4);
		
		// set font size
		$pdf->SetFont('helvetica', '', 10);
		// set color for background
		$pdf->SetFillColor(220, 255, 220);
		
		// Vertical alignment
		$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - TOP] '.$txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'T');
		$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - MIDDLE] '.$txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'M');
		$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - BOTTOM] '.$txt, 1, 'J', 1, 1, '', '', true, 0, false, true, 40, 'B');
		
		$pdf->Ln(4);
		
		*/
		
		
		/*
		* Create content real in pdf
		*
		*/
		
		// create multicell
		$this->createMultiCell($pdf,$template,$items);
		
		
		
		// reset pointer to the last page
		$pdf->lastPage();
		
		// ---------------------------------------------------------
		
		//Close and output PDF document
		$content_pdf=$pdf->Output(JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'docs'.DS.'example_006_jquery.pdf', 'S');
		
		//return true;
		
		$path_pdf = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'docs'.DS.'example_006_jquery.pdf';
		//echo $path;die;
		if ( !JFile::exists($path_pdf) ) {
			echo "File not exist !";
				exit();
		}
		else
			JFile::write($path_pdf, $content_pdf);
		
		//echo 'administrator/components/com_netbasevm_extend/assets/docs/example_006_jquery.pdf';
		//exit();
		// reset again file
		/*
		$file_pdf = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'docs'.DS.'example_006_jquery.pdf';
		if ( JFile::exists($file_pdf) ) {
				echo $file_pdf;die;
				$file_pdf = reset($file_pdf);
		}
		*/
		//============================================================+
		// END OF FILE                                                
		//============================================================+


	}
	
	static function getBasePdfSubdir()
	{
		static $tmpDir;
		
		if (isset($tmpDir))
			return $tmpDir;
		
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.folder');
		
		// define folder for storing invoices
		$mainframe = JFactory::getApplication();
		$tmp = JPath::clean(trim($mainframe->getCfg('tmp_path') ? $mainframe->getCfg('tmp_path') : $mainframe->getCfg('config.tmp_path')));
		$tmp = rtrim($tmp, DS).DS;

		//note: replace space by underscore, TCPDF can have problem with it
		//http://www.artio.net/support-forums/vm-invoice/customer-support/tcpdf-error-pri-vice-polozkach-kosiku
		
				
		$invoicesSubDir = $tmp.str_replace(' ', '_', 'VM '.trim(JText::_('COM_NETBASEVM_EXTEND_INVOICES'),'*').DS);
		
		
		$invoicesSubDir = JPath::clean($invoicesSubDir);
		
		//echo $invoicesSubDir;die;
		//base: no subdir for Netbase VM Extends, use tmp root (but should be always writeable!)
		$tmpDir = $tmp; 
		
		if ($tmpDir){
			if (!JFolder::exists($invoicesSubDir)){ //tmp directory for VM invoices not exists
				if(JFolder::create($invoicesSubDir) && is_writable($invoicesSubDir))
					$tmpDir = $invoicesSubDir;
			}
			else { //directory exists
				if (is_writable($invoicesSubDir))
					$tmpDir = $invoicesSubDir;
			}
		}
		
		if (!is_writable($tmpDir)){
			JError::raiseWarning(0, 'Netbase VM Extend: Tmp directory '.str_replace(JPATH_SITE, '', $tmpDir).' is not writable. Without writable folder invoices cannot be created. Check you have properly set Path to Temp folder in your Joomla! Server configuration and this directory have write permissions (System Information -> Directory Permissions).');
			$tmpDir = false;}
			
		/*
		 if ((!$dirname || !is_writeable(JPath::clean($dirname))) && function_exists('sys_get_temp_dir')) //if not, use systems temp folder
		$dirname = sys_get_temp_dir();
		*/

		return $tmpDir;
	}
	
	// convert rbg to #
	function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
         return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}
	
	// create multicell with params
	function createMultiCell($pdf,$template='',$items='')
	{
		
		// check have data or not
		if(count($template) > 0)
		{
			// get symbol 
			foreach($template as $symbol)
			{
				
				switch($symbol['name'])
				{
					case '{logo}':
					
						//print_r($symbol);die;
						// set fontsize
						$fontsize=$symbol['fontsize_'];
						$pdf->SetFont('helvetica', '', $fontsize);
						
						$pdf->setCellPaddings(1, 1, 1, 1);
						// set cell margins
						$pdf->setCellMargins(0, 0, 0, 0);
						
						// width
						$width=$symbol['width'];
						// height
						$height=$symbol['height'];
						
						// text
						$text=$symbol['name'];
						
						
						$logo='<img src="'.K_PATH_IMAGES.PDF_HEADER_LOGO.'" />';
						// set color for background
						$bgcolor=$symbol['bgcolor'];
						$bgcolor_=$this->hex2rgb($bgcolor);
						$pdf->SetFillColor($bgcolor_['red'],$bgcolor_['green'],$bgcolor_['blue']);
						
						// align
						$align=$symbol['align'];
						
						if($align == 'left')
							$align='L';
						else if($align == 'right')
							$align='R';
						else
							$align='C';
						
							
						//$border=array('B'=>array('width'=>1,'color'=>'#000'));
						$pdf->MultiCell($width, $height,$text, 0, $align, 1, 0, '', '', true,0,true);
						
						break;
					case '{space}':
					
						//print_r($symbol);die;
						// set fontsize
						$fontsize=$symbol['fontsize_'];
						$pdf->SetFont('helvetica', '', $fontsize);
						
						$pdf->setCellPaddings(0, 0, 0, 0);
						// set cell margins
						$pdf->setCellMargins(0, 0, 0, 0);
						
						// width
						$width=$symbol['width'];
						// height
						$height=$symbol['height'];
						
						// text
						$text=$symbol['name'];
						// set color for background
						
						$pdf->SetFillColor(255,255,255);
						
						// align
						$align=$symbol['align'];
						
						if($align == 'left')
							$align='L';
						else if($align == 'right')
							$align='R';
						else
							$align='C';
						
							
						//$border=array('B'=>array('width'=>1,'color'=>'#000'));
						$pdf->MultiCell($width, $height,'', 0, $align, 1, 0, '', '', true,0,true);
						
						break;
					case '{break}':
					  $pdf->Ln();
					  break;
					 case '{products}':
					   // show items
					   // show labels items
					   if(count($items) > 0)
					   {
						   
						  $this->createMultiCell($pdf,$items);
						  
					   }
					   
					 break; 
					 
					case '{custom_text}':
						$fontsize=$symbol['fontsize_'];
						$pdf->SetFont('helvetica', '', $fontsize);
						
						$pdf->setCellPaddings(1, 1, 1, 1);
						// set cell margins
						$pdf->setCellMargins(0, 0, 0, 0);
						
						// width
						$width=$symbol['width'];
						// height
						$height=$symbol['height'];
						
						// text
						$color=$symbol['color'];
						
						$color_=$this->hex2rgb($color);
						$pdf->SetTextColor($color_['red'],$color_['green'],$color_['blue']);
						// set color for background
						$bgcolor=$symbol['bgcolor'];
						$bgcolor_=$this->hex2rgb($bgcolor);
						$pdf->SetFillColor($bgcolor_['red'],$bgcolor_['green'],$bgcolor_['blue']);
						
						// show label
						$show_label=$symbol['show_label'];
						$text=$symbol['name'];
						$label=$symbol['label_'];
						
						// align
						$align=$symbol['align'];
						
						if($align == 'left')
							$align='L';
						else if($align == 'right')
							$align='R';
						else
							$align='C';
						
						/*
						if($show_label == 'yes')
						{
							if($label != '')
							{
								// create cell for label
								$pdf->MultiCell($width, $height,$label, 0, $align, 1, 0, '', '', true,0,true);
								$pdf->Ln();
							}
						}
						else
							$text=$label.$text;
						*/
						// create cell for text	
						//$border=array('B'=>array('width'=>1,'color'=>'#000'));
						$pdf->MultiCell($width, $height,$label, 0, $align, 1, 0, '', '', true,0,true);
						break;   
					default:
						$fontsize=$symbol['fontsize_'];
						$pdf->SetFont('helvetica', '', $fontsize);
						
						$pdf->setCellPaddings(1, 1, 1, 1);
						// set cell margins
						$pdf->setCellMargins(0, 0, 0, 0);
						
						// width
						$width=$symbol['width'];
						// height
						$height=$symbol['height'];
						
						// text
						$color=$symbol['color'];
						
						$color_=$this->hex2rgb($color);
						$pdf->SetTextColor($color_['red'],$color_['green'],$color_['blue']);
						// set color for background
						$bgcolor=$symbol['bgcolor'];
						$bgcolor_=$this->hex2rgb($bgcolor);
						$pdf->SetFillColor($bgcolor_['red'],$bgcolor_['green'],$bgcolor_['blue']);
						
						// show label
						$show_label=$symbol['show_label'];
						$text=$symbol['name'];
						$label=$symbol['label_'];
						
						// align
						$align=$symbol['align'];
						
						if($align == 'left')
							$align='L';
						else if($align == 'right')
							$align='R';
						else
							$align='C';
						
						/*
						if($show_label == 'yes')
						{
							if($label != '')
							{
								// create cell for label
								$pdf->MultiCell($width, $height,$label, 0, $align, 1, 0, '', '', true,0,true);
								$pdf->Ln();
							}
						}
						else
							$text=$label.$text;
						*/
						// create cell for text	
						//$border=array('B'=>array('width'=>1,'color'=>'#000'));
						$pdf->MultiCell($width, $height,$text, 0, $align, 1, 0, '', '', true,0,true);
						
						break;	
					
				}
				
			}
			// end get symbol
		}
		
	}
	
	// create cell items
	function createMultiCellItems($pdf,$items)
	{
		// check have data or not
		if(count($items) > 0)
		{
			// get symbol 
			$i=1;
			foreach($items as $symbol)
			{
				$fontsize=$symbol['fontsize_'];
				$pdf->SetFont('helvetica', '', $fontsize);
				
				$pdf->setCellPaddings(5, 5, 5, 5);
				// set cell margins
				//$pdf->setCellMargins(0, 0, 0, 0);
				
				// width
				$width=$symbol['width'];
				// height
				$height=$symbol['height'];
				
				// text
				$color=$symbol['color'];
				
				$color_=$this->hex2rgb($color);
				$pdf->SetTextColor($color_['red'],$color_['green'],$color_['blue']);
				// set color for background
				$bgcolor=$symbol['bgcolor'];
				$bgcolor_=$this->hex2rgb($bgcolor);
				$pdf->SetFillColor($bgcolor_['red'],$bgcolor_['green'],$bgcolor_['blue']);
				
				// show label
				$show_label=$symbol['show_label'];
				$text=$symbol['name'];
				$label=$symbol['label_'];
				
				// align
				$align=$symbol['align'];
				
				if($align == 'left')
					$align='L';
				else if($align == 'right')
					$align='R';
				else
					$align='C';
				
					
				//$border=array('B'=>array('width'=>1,'color'=>'#000'));
				$pdf->MultiCell($width, $height,$text, 1, $align, 1, 0, '', '', true,0,true);
				
				$i++;
			}
		}
	}
	
	// save templates pdf
	function savePDF()
	{
		$html=$_POST['texthtml'];

		//$html=trim($html,'');
		//$arr=explode('\',$html);
		//$t=str_replace('\"','\',$html);
		
		//print_r(gettype($html));
		$arr=explode('\"',$html);
		$html=implode('"',$arr);
		//echo $html;
		//exit();

		$html_product=$_POST['textproduct'];
		$arr_pro=explode('\"',$html_product);
		$html_product=implode('"',$arr_pro);



		$template=$_POST['template'];
		$items=$_POST['items'];
		
		jimport('joomla.filesystem.file');
		
		
		// write file template_general.html
		$path = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_general.html';
		//echo $path;die;
		if ( !JFile::exists($path) ) {
			echo "File not exist !";
				exit();
		}
		else
			JFile::write($path, $html);
		
		// write file template_product.html
		$path_product = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_product.html';
		if ( !JFile::exists($path_product) ) {
				echo "File not exist !";
				exit();
		}
		else
			JFile::write($path_product, $html_product);
			
		
		// save content file json
		$template_json=json_encode($template);
		$path = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_general.json';
		//echo $path;die;
		if ( !JFile::exists($path) ) {
			echo "File not exist !";
				exit();
		}
		else
			JFile::write($path, $template_json);
		
		// write file template_product.html
		$items_json=json_encode($items);
		$path_product = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_product.json';
		if ( !JFile::exists($path_product) ) {
				echo "File not exist !";
				exit();
		}
		else
			JFile::write($path_product, $items_json);	
		
		
		
	}
	
	
	
}
