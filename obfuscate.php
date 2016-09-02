<?
defined( '_JEXEC' ) or die( 'Restricted access' );
//-------------------------------
$offset_obfus = 0;
//-------------------------------
class plgContentObfuscate extends JPlugin
{
	function onContentPrepare($context,  &$row, &$params, $page) 
	{
		global $contx, $offset_obfus;
		$offset_obfus  = 0;
		/***************************************************/
		$text = $row->text;
		$input = JFactory::getApplication()->input;
		$contx = $context;
		$regex = "#(\{obfuscate\}(.+)\{\/obfuscate})#";
		$row->text = preg_replace_callback($regex, array($this, 'obfuscate_replacer'), $row->text);
		return true;
		/***************************************************/
	}
	
	function obfuscate_replacer(&$matches)
	{
		global $contx, $offset_obfus;
		//-------------------------------
		$str = $matches[2];
		$offset_obfus = 0;
		//-------------------------------
		
		$html = "";
		while ($offset_obfus >= 0) {
			$html .= "&#".ordutf8($str, $offset_obfus).";";
		}
		return $html;
	}
	
}
if(!function_exists("ordutf8")) {
	function ordutf8($string, &$offset_obfus) {
		$code = ord(substr($string, $offset_obfus,1)); 
		if ($code >= 128) {        //otherwise 0xxxxxxx
			if ($code < 224) $bytesnumber = 2;                //110xxxxx
			else if ($code < 240) $bytesnumber = 3;        //1110xxxx
			else if ($code < 248) $bytesnumber = 4;    //11110xxx
			$codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
			for ($i = 2; $i <= $bytesnumber; $i++) {
				$offset_obfus ++;
				$code2 = ord(substr($string, $offset_obfus, 1)) - 128;        //10xxxxxx
				$codetemp = $codetemp*64 + $code2;
			}
			$code = $codetemp;
		}
		$offset_obfus += 1;
		if ($offset_obfus >= strlen($string)) $offset_obfus = -1;
		return $code;
	}
}
?>