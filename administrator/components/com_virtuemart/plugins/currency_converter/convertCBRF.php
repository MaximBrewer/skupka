<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* ������ ���������� ����� ���� ��� Virtuemart 2.0.x, 
* ������� �� ������������ convertECB.php �� Virtuemart 2.0.18a
* ���� = ����� ���� �� �������! ����
* ������ �������� ��������� ����������, � ������ ��� �����������
* �� ����������� ��� ������������ �������������.
* ��������� ������ ����� ����� �� ������:
* http://unboxit.ru
*
* ������: 2.0.1 �� 2015-03-02
* ������������� �� ����� ������.������: 41001164449086
*
*/

class convertCBRF {

// 	var $archive = true;
// 	var $last_updated = '';

	
	var $document_address = 'http://www.cbr.ru/scripts/XML_daily.asp';	
	var $info_address = 'http://www.cbr.ru';
	var $supplier = '����������� ���� ���������� ���������';

	/**
	 * Converts an amount from one currency into another using
	 * the rate conversion table from the European Central Bank
	 *
	 * @param float $amountA
	 * @param string $currA defaults to $vendor_currency
	 * @param string $currB defaults to
	 * @return mixed The converted amount when successful, false on failure
	 */
// 	function convert( $amountA, $currA='', $currB='', $a2b = true ) {
	function convert( $amountA, $currA='', $currB='', $a2rC = true, $relatedCurrency = 'EUR') {

		// cache subfolder(group) 'convertCBRF', cache method: callback
		$cache= JFactory::getCache('convertCBRF','callback');

		// save configured lifetime
		@$lifetime=$cache->lifetime;

		$cache->setLifeTime(86400/24); // check 24 time per day

		// save cache conf

		$conf = JFactory::getConfig();

		// check if cache is enabled in configuration

		$cacheactive = $conf->get('caching');

		$cache->setCaching(0); //����������� ���������!

		$globalCurrencyConverter = $cache->call( array( 'convertCBRF', 'getSetExchangeRates' ),$this->document_address );

		// revert configuration

		$cache->setCaching($cacheactive);

		if(!$globalCurrencyConverter ){
			//vmdebug('convert convert No $globalCurrencyConverter convert '.$amountA);
			return $amountA;
		} else {
			$valA = isset( $globalCurrencyConverter[$currA] ) ? $globalCurrencyConverter[$currA] : 1.0;
			$valB = isset( $globalCurrencyConverter[$currB] ) ? $globalCurrencyConverter[$currB] : 1.0;

			$val = (float)$amountA * (float)$valB / (float)$valA;
			//vmdebug('convertCBRF with '.$currA.' '.$amountA.' * '.$valB.' / '.$valA.' = '.$val,$globalCurrencyConverter[$currA]);

			return $val;
		}
	}

	static function getSetExchangeRates($cbrf_filename){
			$archive = true;
			setlocale(LC_TIME, "en-GB");
			$chas_p = 4;			// ����� MSK: +4GMT
			$now = time() + $chas_p*3600; 
			
			/* ��� �������� ������ - ������ �����.
			if (gmdate("I")) {
				$now += 3600;
			}
			*/
			
			$weekday_now_local = gmdate('w', $now); // ���� ������, ������ ���������� � ����������� (= 0) !!
			$date_now_local = gmdate('Ymd', $now);
			$time_now_local = gmdate('Hi', $now);
			
			
			
			if( is_writable(JPATH_BASE.DS.'cache') ) {
				$store_path = JPATH_BASE.DS.'cache';
			}
			else {
				$store_path = JPATH_SITE.DS.'media';
			}

			$archivefile_name = $store_path.'/daily_CBRF.xml';

			$val = '';


			if(file_exists($archivefile_name) && filesize( $archivefile_name ) > 0 ) {
				// timestamp for the Filename
				$file_datestamp = gmdate('Ymd', filemtime($archivefile_name) + $chas_p*3600);


				if($file_datestamp != $date_now_local){
					$curr_filename = $cbrf_filename.'?date_req='.gmdate( 'd/m/Y', $now );
				}
				else 
					{
					$curr_filename = $archivefile_name;
					$last_updated = $file_datestamp;
					$archive = false;
					}
			}
			else 
				{
				$curr_filename = $cbrf_filename.'?date_req='.gmdate( 'd/m/Y', $now );
				}

			if( !is_writable( $store_path )) {
				$archive = false;
				vmError( "The file $archivefile_name can't be created. The directory $store_path is not writable" );
			}
			//			JError::raiseNotice(1, "The file $archivefile_name should be in the directory $store_path " );
			if( $curr_filename == $cbrf_filename ) {
				// Fetch the file from the internet
				if(!class_exists('VmConnector')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'connection.php');
				//				JError::raiseNotice(1, "Updating currency " );
				if (!$contents = VmConnector::handleCommunication( $curr_filename )) {
					if (isset($file_datestamp)) {
						$contents = @file_get_contents( $curr_filename );
					}
				} else $last_updated = gmdate('Ymd', $now);

			}
			else {
				$contents = @file_get_contents( $curr_filename );
			}
			if( $contents ) {
				// ���� ���� ������ ����
				if( $archive ) {
					// �� ����� ��� :)
					file_put_contents( $archivefile_name, $contents );
				}

				//���������� ������ � �����, �.�. ������� ������ ����� - 1 �����
				$add_str = '<Valute ID="xxx"><NumCode>yyy</NumCode><CharCode>RUB</CharCode><Nominal>1</Nominal><Name>���������� �����</Name><Value>1</Value></Valute>'; 
				$contents = str_replace ("</ValCurs>", "$add_str </ValCurs>", $contents); //��������� ���� ����������� � �����

				
				// �� ����������� ��������� ���� 3 ��������� ����� ������ (����/ USD)
				preg_match_all ("/<CharCode>([A-Z]{3})<\/CharCode>/", $contents, $char_arr);
				
				// �� ����������� ��������� ���� ����� ��������  � �������, ������� ������
				preg_match_all ("/<Nominal>(\d+\,?\d*)<\/Nominal>/", $contents, $nom_arr);

				// �� ����������� ��������� ���� ����� �������� � �������, ���� ������ 
				// (�.�. �� ������� �������� ����� ������ ��������� ������)
				preg_match_all ("/<Value>(\d+\,?\d*)<\/Value>/", $contents, $val_arr);	

				//��������� ������ �����
				for ($i = 0; $i < count($char_arr[1]); $i++)
				{
				//�.�. �� 1 ����� ����� ������ = ������� ������ / ���� ������
				$currency [$char_arr[1][$i]] = $nom_arr[1][$i]/(str_replace (',', '.', $val_arr[1][$i])); // ����������� � ����� ������� "," � ����� "."
				}
				
				
				$globalCurrencyConverter = $currency;
			}
			else {
				$globalCurrencyConverter = false;
				vmError( 'Failed to retrieve the Currency Converter XML document.');
// 				return false;
			}

			return $globalCurrencyConverter;
	}

}
// pure php no closing tag
