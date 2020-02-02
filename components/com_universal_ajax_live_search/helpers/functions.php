<?php
/*------------------------------------------------------------------------
# com_universal_ajaxlivesearch - Universal AJAX Live Search
# ------------------------------------------------------------------------
# author    Janos Biro
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!function_exists('parseParams')){
  function parseParams(&$p, $vals){
    if(version_compare(JVERSION,'1.6.0','>=')) {
      $p->loadJSON($vals);
    }else{
      $p->loadIni($vals);
    }
  }
}

if(!function_exists('buildPluginNameArray')){
  function buildPluginNameArray($a){
    $newa = array();
    $tmp = '';
    foreach($a AS $k => $v){
      ($k % 2 == 0) ? $tmp = $v : $newa[$tmp] = $v;
    }
    return $newa;
  }
}

// If json_encode is not defined - PHP4
if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}


/**
 * LiveSearch component helper.
 */

class LiveSearchHelper
{

  public static function prepareSearchContent($text, $searchword, $length)
  {
    $searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $searchword);
    $searchwords = preg_split("/\s+/u", $searchworda);
    $searchwords = array_values(array_unique($searchwords));
    $needle      = $searchwords[0];
    // Strips tags won't remove the actual jscript
    $text = preg_replace("'<script[^>]*>.*?</script>'si", "", $text);
    $text = preg_replace('/{.+?}/', '', $text);

    // $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is','\2', $text);

    // Replace line breaking tags with whitespace
    $text = preg_replace("'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text);
    $row = self::_smartSubstr(strip_tags($text), $needle, $length);
    $srow = strtolower(SearchHelper::remove_accents($row));
    return self::highLightContent($srow, $row, $searchwords);
  }

  public static function highLightContent($srow, $row, $searchwords){
    $hl1         = '<([{';
		$hl2         = '}])>'; 
    $hlstart     = '<strong class="highlight">';
    $hlend       = '</strong>';
    $cnt         = 0;

    foreach ($searchwords as $hlword)
    {
      
      $needle = strtolower(SearchHelper::remove_accents($hlword));
      if ($needle && ($pos = mb_strpos($row, $needle)) !== false)
      {
     //   $pos += $cnt++ * mb_strlen($hl1 . $hl2);

        // iconv transliterates '€' to 'EUR'
        // TODO: add other expanding translations?
        $eur_compensation = $pos > 0 ? substr_count($row, "\xE2\x82\xAC", 0, $pos) * 2 : 0;
        $pos -= $eur_compensation;
        $row = mb_substr($row, 0, $pos) . $hl1 . mb_substr($row, $pos, mb_strlen($hlword)) . $hl2 . mb_substr($row, $pos + mb_strlen($hlword));
      }
    }
    $row = str_replace(array($hl1,$hl2),array($hlstart,$hlend),$row);
    return $row;
  }

  /**
   * returns substring of characters around a searchword
   *
   * @param   string   $text        The source string
   * @param   integer  $searchword  Number of chars to return
   *
   * @return  string
   *
   * @since   1.5
   */
  public static function _smartSubstr($text, $searchword, $length)
  {
    $lang        = JFactory::getLanguage();
    $ltext       = SearchHelper::remove_accents($text);
    $textlen     = JString::strlen($ltext);
    $lsearchword = JString::strtolower(SearchHelper::remove_accents($searchword));
    $wordfound   = false;
    $pos         = 0;

    while ($wordfound === false && $pos < $textlen)
    {
      if (($wordpos = @JString::strpos($ltext, ' ', $pos + $length)) !== false)
      {
        $chunk_size = $wordpos - $pos;
      }
      else
      {
        $chunk_size = $length;
      }

      $chunk     = JString::substr($ltext, $pos, $chunk_size);
      $wordfound = JString::strpos(JString::strtolower($chunk), $lsearchword);

      if ($wordfound === false)
      {
        $pos += $chunk_size + 1;
      }
    }

    if ($wordfound !== false)
    {
      return (($pos > 0) ? '...&#160;' : '') . JString::substr($text, $pos, $chunk_size) . '&#160;...';
    }
    else
    {
      if (($wordpos = @JString::strpos($text, ' ', $length)) !== false)
      {
        return JString::substr($text, 0, $wordpos) . '&#160;...';
      }
      else
      {
        return JString::substr($text, 0, $length);
      }
    }
  }

}

?>