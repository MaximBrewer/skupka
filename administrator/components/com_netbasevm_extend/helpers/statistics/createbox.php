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

abstract class basicBox
{
	public $response;
	private $titleLeftClass;
	private $titleRightClass;
	private $titleMiddleClass;
	private $bottomLeftClass;
	private $bottomRightClass;
	private $bottomMiddleClass;
	private $borderRightClass;
	private $borderLeftClass;
	private $alignleft;
	private $aligntight;
	private $boxContent;
	private $contentAlign; //if pass NULL in c'tor content alignment will be as the context
	private static $instanceCounter = 0;
	
	//protected abstract methods
	abstract protected function drawTitle();
	abstract protected function drawFooter();
	abstract protected function drawBorders();
	abstract protected function closeBorders();
	abstract protected function drawBody();

	//public metods
	public function createBox()
	{
		$this->response = "<div id=\"box_" . ++self::$instanceCounter . "\">";
		$this->drawTitle();
		$this->drawBorders();
		$this->drawBody();
		$this->closeBorders();
		$this->drawFooter();
		$this->response .= "</div>";
	}
	
	public static function resetInstanceCounter()
	{
		self::$instanceCounter = 0;
	}

}

abstract class box extends basicBox
{
	//protected common methods
	protected function drawTitle()
	{
		$this->response .= "<table cellspacing=\"0px\" cellpadding=\"0px\" class=\"basicBoxTitle\" border=\"0\">";
		$this->response .=  "<tr>";
		$this->response .=  "<td width=\"\" class =\"" . $this->titleLeftClass . "\" valign=\"bottom\"></td>";
		$this->response .=  "<td width=\"\" class =\"" . $this->titleMiddleClass . "\" >";
		$this->response .=  "&nbsp;</td>";
		$this->response .=  "<td width=\"\" class =\"" . $this->titleRightClass . "\"/>";
		$this->response .= "</tr>";
		$this->response .= "</table>";
	}
	
	protected function drawFooter()
	{
		$this->response .= "<table cellspacing=\"0px\" cellpadding=\"0px\" class=\"basicBoxFooter\" border=\"0\"><tr><td class=\"" . $this->bottomLeftClass . "\" width=\"\" ></td><td class=\"" . $this->bottomMiddleClass . "\" width=\"\" >&nbsp;</td><td class=\"" . $this->bottomRightClass . "\" width=\"\" ></td></tr></table>";
	}
	
	protected function drawBorders()
	{
		$this->response .= "<div class=\"" . $this->borderLeftClass . "\"><div class=\"" . $this->borderRightClass . "\">";
	}
	
	protected function closeBorders()
	{
		$this->response .= "</div></div>";
	}
	
	protected function drawBody()
	{
		$this->response .= "<table cellspacing=\"0px\" cellpadding=\"0px\" class=\"basicBoxBody\" border=\"0\">";
	
		$this->response .= "<tr>";
		$this->response .= "<td class=\"basicBoxBodyText\" valign=\"top\" align=\"left\">";
		$this->response .= $this->boxContent;
		$this->response .= "</td>";
		$this->response .= "</tr>";
		
		$this->response .= "</table>";
	}
	
	//public metods
	
	public function createGetBox()
	{
		$this->createBox();
		return $this->response;
	}
	
	public function __toString()
	{
		return $this->response;
	}
	
	public static function newBox($content, $title = NULL, $boxType = NULL)
	{
		switch ($boxType)
		{
			case "fat" :
				$obj = new fatBox($content);
				break;
			case "plain" :
				$obj = new plainBox($content);
				break;
			case "title" :
				$obj = new titleBox($content,$title);
				break;
			case "title_red" :
				$obj = new titleBox_red($content,$title);
				break;
			default:
				$obj = new plainBox($content);
		}
		return $obj;
	}
} 
class fatBox extends box
{
		public function __construct($content)	
		{
			//if($dir == "ltr" || !isset($dir) || $dir==NULL)
			//{
				$this->alignleft = "left";
				$this->aligntight = "right";
				$this->titleLeftClass = "BBTitleLeft";
				$this->titleRightClass = "BBTitleRight";
				$this->bottomLeftClass = "BBBottomLeft";
				$this->bottomRightClass = "BBBottomRight";
			//}
			/*else
			{
				$this->alignleft = "right";
				$this->aligntight = "left";
				$this->titleLeftClass = "BBTitleRight";
				$this->titleRightClass = "BBTitleLeft";
				$this->bottomLeftClass = "BBBottomRight";
				$this->bottomRightClass = "BBBottomLeft";
			}	*/
			
			//$this->contentAlign = $contentAlign == NULL ? $this->alignleft : $contentAlign;
			$this->titleMiddleClass = "BBTitleMiddle";
			$this->bottomMiddleClass = "BBBottomMiddle";
			$this->borderRightClass = "boxBodyRight";
			$this->borderLeftClass = "boxBodyLeft";
			
			$this->response = "";
			$this->boxContent = $content;
		}
}

class plainBox extends box
{
		public function __construct($content)	
		{
			//if($dir == "ltr" || !isset($dir) || $dir==NULL)
			//{
				$this->alignleft = "left";
				$this->aligntight = "right";
				$this->titleLeftClass = "PBTitleLeft";
				$this->titleRightClass = "PBTitleRight";
				$this->bottomLeftClass = "PBBottomLeft";
				$this->bottomRightClass = "PBBottomRight";
			//}
			/*else
			{
				$this->alignleft = "right";
				$this->aligntight = "left";
				$this->titleLeftClass = "PBTitleRight";
				$this->titleRightClass = "PBTitleLeft";
				$this->bottomLeftClass = "PBBottomRight";
				$this->bottomRightClass = "PBBottomLeft";
			}	*/
			//$this->contentAlign = $contentAlign == NULL ? $this->alignleft : $contentAlign;
			$this->titleMiddleClass = "PBTitleMiddle";
			$this->bottomMiddleClass = "PBBottomMiddle";
			$this->borderRightClass = "boxBodyRight";
			$this->borderLeftClass = "boxBodyLeft";
			
			$this->response = "";
			$this->boxContent = $content;
		}
}

class titleBox extends box
{
		private $boxTitle;
		
		public function __construct($content, $title)	
		{
			
			//if($dir == "ltr" || !isset($dir) || $dir==NULL)
			//{
				$this->alignleft = "left";
				$this->aligntight = "right";
				$this->titleLeftClass = "tbTitleLeft";
				$this->titleRightClass = "tbTitleRight";
				$this->bottomLeftClass = "tbBottomLeft";
				$this->bottomRightClass = "tbBottomRight";
			//}
			/*else
			{
				$this->alignleft = "right";
				$this->aligntight = "left";
				$this->titleLeftClass = "BBTitleRight";
				$this->titleRightClass = "BBTitleLeft";
				$this->bottomLeftClass = "BBBottomRight";
				$this->bottomRightClass = "BBBottomLeft";
			}	*/
			//$this->contentAlign = $contentAlign == NULL ? $this->alignleft : $contentAlign;
			$this->titleMiddleClass = "tbTitleMiddle";
			$this->bottomMiddleClass = "tbBottomMiddle";
			$this->borderRightClass = "boxBodyRight";
			$this->borderLeftClass = "boxBodyLeft";
			
			$this->response = "";
			$this->boxContent = $content;
			$this->boxTitle = $title;
		}
		
		protected function drawTitle()
		{
			$this->response .= "<table cellspacing=\"0px\" cellpadding=\"0px\" class=\"tbTitle\" border=\"0\">";
			$this->response .=  "<tr>";
			$this->response .=  "<td width=\"\" class =\"" . $this->titleLeftClass . "\" valign=\"bottom\"></td>";
			$this->response .=  "<td width=\"\" class =\"" . $this->titleMiddleClass . "\"><h5>" . $this->boxTitle . "</h5></td>";
			$this->response .=  "<td width=\"\" class =\"" . $this->titleRightClass . "\" valign=\"bottom\"></td>";
			$this->response .=  "</tr>";
			$this->response .= "</table>";
		}
}

class titleBox_red extends box
{
		private $boxTitle;
		
		public function __construct($content, $title)	
		{
			
			//if($dir == "ltr" || !isset($dir) || $dir==NULL)
			//{
				$this->alignleft = "left";
				$this->aligntight = "right";
				$this->titleLeftClass = "tbTitleLeftRed";
				$this->titleRightClass = "tbTitleRightRed";
				$this->bottomLeftClass = "tbBottomLeftRed";
				$this->bottomRightClass = "tbBottomRightRed";
			//}
			/*else
			{
				$this->alignleft = "right";
				$this->aligntight = "left";
				$this->titleLeftClass = "BBTitleRight";
				$this->titleRightClass = "BBTitleLeft";
				$this->bottomLeftClass = "BBBottomRight";
				$this->bottomRightClass = "BBBottomLeft";
			}	*/
			//$this->contentAlign = $contentAlign == NULL ? $this->alignleft : $contentAlign;
			$this->titleMiddleClass = "tbTitleMiddleRed";
			$this->bottomMiddleClass = "tbBottomMiddleRed";
			$this->borderRightClass = "boxBodyRight";
			$this->borderLeftClass = "boxBodyLeft";
			
			$this->response = "";
			$this->boxContent = $content;
			$this->boxTitle = $title;
		}
		
		protected function drawTitle()
		{
			$this->response .= "<table cellspacing=\"0px\" cellpadding=\"0px\" class=\"tbTitle\" border=\"0\">";
			$this->response .=  "<tr>";
			$this->response .=  "<td width=\"\" class =\"" . $this->titleLeftClass . "\" valign=\"bottom\"></td>";
			$this->response .=  "<td width=\"\" class =\"" . $this->titleMiddleClass . "\"><h5>" . $this->boxTitle . "</h5></td>";
			$this->response .=  "<td width=\"\" class =\"" . $this->titleRightClass . "\" valign=\"bottom\"></td>";
			$this->response .=  "</tr>";
			$this->response .= "</table>";
		}
}

?>