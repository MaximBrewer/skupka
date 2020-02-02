<?php
defined ('_JEXEC') or die();
if(version_compare(JVM_VERSION,'3','ge')){
	JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldVmRobokassaCurrency extends JFormFieldList {

	/**
	 * Element name
	 * @access    protected
	 * @var        string
	 */
	var $type = 'RobokassaCurrency';

	protected function getOptions() {
		$options = array(array('value'=>0,'text'=>'Выбор на сайте Робокассы'));
		$values = vmRobokassaCurrency();
		foreach ($values as $v) {
			$options[] = JHtml::_('select.option', $v['Label'], $v['Name']);
		}

		//BAD $class = 'multiple="true" size="10"';
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
} else {
class JElementVmRobokassaCurrency extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'RobokassaCurrency';

	function fetchElement ($name, $value, &$node, $control_name) {
		$options = array(array('value'=>0,'text'=>'Выбор на сайте Робокассы'));
		$values = vmRobokassaCurrency();
		foreach ($values as $v) {
			$options[] = JHtml::_('select.option', $v['Label'], $v['Name']);
		}
		return JHtml::_('select.genericlist', $options, $control_name . '[' . $name . ']', '','value', 'text',  $value, $control_name . $name);
	}

}

}

function vmRobokassaCurrency(){
	jimport( 'joomla.client.http' );
		$opt = new JRegistry;
		if (function_exists('curl_version') && curl_version()){
			$trans = new JHttpTransportCurl($opt);
		} elseif (function_exists('fopen') && is_callable('fopen') && ini_get('allow_url_fopen')){
			$trans = new JHttpTransportStream($opt);
		} elseif(function_exists('fsockopen') && is_callable('fsockopen')){
			$trans = new JHttpTransportSocket($opt);
		} else {
			JError::raiseError(500, "Can't initialise http transport ");
		}
		$http = new JHttp($opt,$trans);
	$cache_file = dirname(__FILE__)."/robokassa_currency.xml";
	if (!file_exists($cache_file)||filemtime($cache_file)+60*60*24<time()) {

		$result = $http->get("https://auth.robokassa.ru/Merchant/WebService/Service.asmx/GetCurrencies?MerchantLogin=demo&Language=ru");
		$xml_str = $result->body;

		$xml = @simplexml_load_string($xml_str);
		if ($xml) {
			file_put_contents($cache_file, $xml_str);
		}
	} else {
		$xml = simplexml_load_file($cache_file);
	}
	$options = array();
	if ($xml){
		foreach($xml->Groups->Group as $group){
			foreach($group->Items->Currency as $item){
				$attrib = $item->attributes();
				$options[] = array('Label'=>strval($attrib['Label']),'Name'=>strval($attrib['Name']));
			}
		}
	} else {
		$options = array(
			 0 => array ( 'Label' => 'Qiwi29OceanR', 'Name' => 'QIWI Кошелек', ),
			 1 => array ( 'Label' => 'TerminalsElecsnetOceanR', 'Name' => 'Элекснет', ),
			 2 => array ( 'Label' => 'TerminalsMElementR', 'Name' => 'Мобил Элемент', ),
			 3 => array ( 'Label' => 'TerminalsKassira.NetOceanR', 'Name' => 'Кассира.нет', ),
			 4 => array ( 'Label' => 'TerminalsFSGorodR', 'Name' => 'Федеральная Система Город', ),
			 5 => array ( 'Label' => 'YandexMerchantR', 'Name' => 'Яндекс.Деньги', ),
			 6 => array ( 'Label' => 'WMRM', 'Name' => 'WMR', ),
			 7 => array ( 'Label' => 'Qiwi29OceanR', 'Name' => 'QIWI Кошелек', ),
			 8 => array ( 'Label' => 'ElecsnetWalletR', 'Name' => 'Кошелек Элекснет', ),
			 9 => array ( 'Label' => 'MailRuOceanR', 'Name' => 'Деньги@Mail.Ru', ),
			 10 => array ( 'Label' => 'WMZM', 'Name' => 'WMZ', ),
			 11 => array ( 'Label' => 'WMUM', 'Name' => 'WMU', ),
			 12 => array ( 'Label' => 'EasyPayB', 'Name' => 'EasyPay', ),
			 13 => array ( 'Label' => 'W1OceanR', 'Name' => 'RUR Единый кошелек', ),
			 14 => array ( 'Label' => 'W1R', 'Name' => 'RUR Единый Кошелек', ),
			 15 => array ( 'Label' => 'TeleMoneyR', 'Name' => 'TeleMoney', ),
			 16 => array ( 'Label' => 'WMEM', 'Name' => 'WME', ),
			 17 => array ( 'Label' => 'WMBM', 'Name' => 'WMB', ),
			 18 => array ( 'Label' => 'BANKOCEAN2R', 'Name' => 'Банковская карта', ),
			 19 => array ( 'Label' => 'AlfaBankOceanR', 'Name' => 'Альфа-Клик', ),
			 20 => array ( 'Label' => 'RussianStandardBankR', 'Name' => 'Банк Русский Стандарт', ),
			 21 => array ( 'Label' => 'SvyaznoyR', 'Name' => 'QBank', ),
			 22 => array ( 'Label' => 'PSKBR', 'Name' => 'Промсвязьбанк', ),
			 23 => array ( 'Label' => 'VTB24R', 'Name' => 'ВТБ24', ),
			 24 => array ( 'Label' => 'OceanBankOceanR', 'Name' => 'Океан Банк', ),
			 25 => array ( 'Label' => 'HandyBankMerchantOceanR', 'Name' => 'HandyBank', ),
			 26 => array ( 'Label' => 'HandyBankBB', 'Name' => 'Банк Богородский', ),
			 27 => array ( 'Label' => 'HandyBankBO', 'Name' => 'Банк Образование', ),
			 28 => array ( 'Label' => 'HandyBankFB', 'Name' => 'ФлексБанк', ),
			 29 => array ( 'Label' => 'HandyBankFU', 'Name' => 'ФьючерБанк', ),
			 30 => array ( 'Label' => 'HandyBankKB', 'Name' => 'КранБанк', ),
			 31 => array ( 'Label' => 'HandyBankKSB', 'Name' => 'Костромаселькомбанк', ),
			 32 => array ( 'Label' => 'HandyBankLOB', 'Name' => 'Липецкий областной банк', ),
			 33 => array ( 'Label' => 'HandyBankNSB', 'Name' => 'Независимый строительный банк', ),
			 34 => array ( 'Label' => 'HandyBankTB', 'Name' => 'Русский Трастовый Банк', ),
			 35 => array ( 'Label' => 'HandyBankVIB', 'Name' => 'ВестИнтерБанк', ),
			 36 => array ( 'Label' => 'BSSMezhtopenergobankR', 'Name' => 'Межтопэнергобанк', ),
			 37 => array ( 'Label' => 'MINBankR', 'Name' => 'Московский Индустриальный Банк', ),
			 38 => array ( 'Label' => 'BSSIntezaR', 'Name' => 'Банк Интеза', ),
			 39 => array ( 'Label' => 'BSSBankGorodR', 'Name' => 'Банк Город', ),
			 40 => array ( 'Label' => 'BSSAvtovazbankR', 'Name' => 'Банк АВБ', ),
			 41 => array ( 'Label' => 'MegafonR', 'Name' => 'Мегафон', ),
			 42 => array ( 'Label' => 'MtsR', 'Name' => 'МТС', ),
			 43 => array ( 'Label' => 'MobicomBeelineR', 'Name' => 'Билайн', ),
			 44 => array ( 'Label' => 'RapidaOceanEurosetR', 'Name' => 'Евросеть', ),
			 45 => array ( 'Label' => 'RapidaOceanSvyaznoyR', 'Name' => 'Связной', ),
			 46 => array ( 'Label' => 'BANKOCEAN2CHECKR', 'Name' => 'Мобильная ROBOKASSA', )
			);
	}
	return $options;
}

