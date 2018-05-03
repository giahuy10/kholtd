<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */

// No direct access
defined('_JEXEC') or die;

/**
 * Onecard helper.
 *
 * @since  1.6
 */
class OnecardHelpersOnecard
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
	    
             JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_BRANDS'),
            'index.php?option=com_onecard&view=brands',
            $vName == 'brands'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_CODES'),
            'index.php?option=com_onecard&view=codes',
            $vName == 'codes'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_CONTRACT'),
            'index.php?option=com_onecard&view=contracts',
            $vName == 'contracts'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_EVENTS'),
            'index.php?option=com_onecard&view=events',
            $vName == 'events'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_EXPORT VOUCHERS'),
            'index.php?option=com_onecard&view=export_vouchers',
            $vName == 'export_vouchers'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_NCC'),
            'index.php?option=com_onecard&view=nccs',
            $vName == 'nccs'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_ORDERS'),
            'index.php?option=com_onecard&view=orders',
            $vName == 'orders'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_ORDER VOUCHERS'),
            'index.php?option=com_onecard&view=order_vouchers',
            $vName == 'order_vouchers'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_PARTNERS'),
            'index.php?option=com_onecard&view=partners',
            $vName == 'partners'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_VOUCHERS'),
            'index.php?option=com_onecard&view=vouchers',
            $vName == 'vouchers'
        );



	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_onecard';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}


class OnecardHelper extends OnecardHelpersOnecard
	
{
	public static function  postCurl($url, $var)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $var,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			return $response;
			
		}
	}
	static function errorType($code)
	{
		switch ($code) {
			case '0':
				return 'Giao dịch chưa xác định (' . $code . ')';
				break;
			case '-1':
				return 'Lỗi hệ thống (' . $code . ')';
				break;
			case '-55':
				return 'Số dư tài khoản không đủ để thực hiện (' . $code . ')';
				break;
			case '-99':
				return 'Lỗi chưa xác định (' . $code . ')';
				break;
			case '-290':
				return 'Thông tin lệnh nạp tiền hợp lệ. Đang chờ kết quả xử lý (' . $code . ')';
				break;
			case '-302':
				return 'Partner không tồn tại hoặc đang tạm dừng hoạt động (' . $code . ')';
				break;
			case '-304':
				return 'Dịch vụ này không tồn tại hoặc đang tạm dừng (' . $code . ')';
				break;
			case '-305':
				return 'Chữ ký không hợp lệ (' . $code . ')';
				break;
			case '-306':
				return 'Mệnh giá không hợp lệ hoặc đang tạm dừng (' . $code . ')';
				break;
			case '-307':
				return 'Tài khoản nạp tiền không tồn tại hoặc không hợp lệ (' . $code . ')';
				break;
			case '-308':
				return 'RequesData không hợp lệ (' . $code . ')';
				break;
			case '-309':
				return 'Ngày giao dịch truyền không đúng (' . $code . ')';
				break;
			case '-310':
				return 'Hết hạn mức cho phép sử dụng dịch vụ này (' . $code . ')';
				break;
			case '-311':
				return 'RequesData hoặc PartnerCode không đúng (' . $code . ')';
				break;
			case '-315':
				return 'Phải truyền CommandType (' . $code . ')';
				break;
			case '-316':
				return 'Phải truyền version (' . $code . ')';
				break;
			case '-317':
				return 'Số lượng thẻ không hợp lệ (' . $code . ')';
				break;
			case '-318':
				return 'ServiceCode không đúng (' . $code . ')';
				break;
			case '-320':
				return 'Hệ thống gián đoạn (' . $code . ')';
				break;
			case '-348':
				return 'Tài khoản bị Block Cho phép hoàn tiên (' . $code . ')';
				break;
			case '-350':
				return 'Tài khoản không tồn tại (' . $code . ')';
				break;
			case '-500':
				return 'Loại thẻ này trong kho hiện đã hết hoặc tạm ngừng xuất (' . $code . ')';
				break;
			case '-501':
				return 'Giao dịch không thành công (' . $code . ')';
				break;
			case '-502':
				return 'Không tồn tại giao dịch (' . $code . ')';
				break;
			case '-503':
				return 'Đối tác không đươc thực hiện chức năng này (' . $code . ')';
				break;
			case '-504':
				return 'Mã giao dịch này đã check quá tối đa số lần cho phép (' . $code . ')';
				break;
			case '-505':
				return 'Số lần check vượt quá hạn mức cho phép trong ngày (' . $code . ')';
				break;
			case '-509':
				return 'Giao dịch bị hủy (thất bại) (' . $code . ')';
				break;
			case '-600':
				return 'Quá hạn mức (' . $code . ')';
				break;
		}
		return 'Lỗi không xác định (' . $code . ')';
	}
	static function Decrypt($input, $key_seed)
	{
		$input = base64_decode($input);
		$key = substr(md5($key_seed), 0, 24);
		$text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');
		$block = mcrypt_get_block_size('tripledes', 'ecb');
		$packing = ord($text {
			strlen($text) - 1});
		if ($packing and ($packing < $block)) {
			for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {
				if (ord($text {
					$P}) != $packing) {
					$packing = 0;
				}
			}
		}
		$text = substr($text, 0, strlen($text) - $packing);
		return $text;
	}
	public static function buy_vtc_code($Quantity, $ServiceCode, $Amount){

		
		require_once(JPATH_COMPONENT . '/libs/Nusoap/nusoap.php');
		
		require_once(JPATH_COMPONENT . '/libs/Crypt/RSA.php');
		
		$OrgTransID = $_SERVER['REQUEST_TIME'];
		$TransDate = date('YmdHis'); // Thời gian
		$partnerCode = '0912345331'; // Partner code
		$private_key = 'MIICXwKBAQACgYDDCJIvWsRva9pYyUyUi+U8m8Mv7O/TkgdF/L4qzgxmmUgYvof3
IdsNvya9LEHMxWBkpvdSOzxged/5GhKh9qtASBpGy05+HJoFurmGen8um8e4j020
gGEfd60LgcLBoipz4uf1N9Zvko9/O4WOLTQCUl35REWF9eICb3rRnWptUwKBAwEA
AQKBgDN4Xhf4NNIQ3QlEapzjRIaXts29klc7+QZr2oXyZcxn1GKPWdOLEEPS9/bB
qMXRKwy1EZ0We+scDtMvIc6zieLRjWFc4WiHoJgQAd7xHF28gABfws8thcAkXqas
f7EiU0glGFOjh6IdMkZMN56h2QiywLgC0ZOSqSrg9ysfNAidAoFA5DNMKAVPJMwa
YlURgkWOL40FL6jmfNbf1zEvx7edh87jonecSjGqdSbuUTwIajTPXUk36sECbugU
3wH6JpcajwKBQNrK7Ir8MdEW0GetDUNPChIbSy6DxqjywoAUM9aLvgJUIEtxuG/1
sfyFZklwqtrW9dwY6R0LbnBe6xVHNBJm8v0CgUBk4ly/sKEthmH/qNYFvpQ+Z1ys
lkHXXPM2YlNaOs2U1Z0DHVfl4REXm69uEFk0AsbN2emzicJ2n3liobAiUVj3AoFA
EZjWk4sbGp0CIASMF4jI35HwZwpUNQxpVlHJpYzRuHA5tLetxNt2+D9mbauxIi69
0XjzbtGXjVQlBi4W4xACpQKBQNl1wNQb82aALaf2xu0JaV+wocDomsOtZSdpqzMe
vlDLIfFJBiZzSUA9pehf0k6mpvZ/BN5VpHASIJl5R7Bpz1U='; // Private key
		$triple_key = 'ff39fc173e7ed3c35e01d139e6042e64'; // TripleDES Key
		$url = 'http://alpha3.vtcpay.vn/ws/GoodsPaygate.asmx'; // Link service
		//Onecardhelper::log_sql("requesData", "1");
//  Mua Card
// Tạo chữ ký
		$rsa = new Crypt_RSA();
		$rsa->loadKey($private_key);
		$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$signature = base64_encode($rsa->sign($ServiceCode . '-' . $Amount . '-' . $Quantity . '-' . $partnerCode . '-' . $TransDate . '-' . $OrgTransID));
// Post data
		//Onecardhelper::log_sql("requesData", "2");
		$client = new nusoap_client($url, true);
//var_dump($client);
		$param['requesData'] = '<?xml version="1.0" encoding="utf-8"?>
       <RequestData xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
       <ServiceCode>' . $ServiceCode . '</ServiceCode>
       <Amount>' . $Amount . '</Amount>
       <Quantity>' . $Quantity . '</Quantity>
       <TransDate>' . $TransDate . '</TransDate>
       <OrgTransID>' . $OrgTransID . '</OrgTransID>
       <DataSign>' . $signature . '</DataSign>
      </RequestData>';
		$param['partnerCode'] = $partnerCode;
		$param['commandType'] = 'BuyCard';
		$param['version'] = '1.0';

		Onecardhelper::log_sql("requesData", $param['requesData']);

		$result = $client->call('RequestTransaction', $param);
		if ($client->fault) {
			//echo 'Error: ';
			return ($result);
		} else {
    // check result
			$err_msg = $client->getError();
			if ($err_msg) {
        // Print error msg
				return 'Error: ' . $err_msg;
			} else {
        // Print result
				//echo 'Result: ';
				return ($result);
			}
		}
		// Băm kết quả
		
		$result = explode('|', $result);
		if ($result[0] == 1) {
	// Giao dịch thành công
	// Tạo chữ ký
			$rsa = new Crypt_RSA();
			$rsa->loadKey($private_key);
			$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
			$signature = base64_encode($rsa->sign($ServiceCode . '-' . $Amount . '-' . $partnerCode . '-' . $result[2]));
	// Post data
			$client = new nusoap_client($url, true);
			$param['requesData'] = '<?xml version="1.0" encoding="utf-8"?>
	       <RequestData xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
			<ServiceCode>' . $ServiceCode . '</ServiceCode>
			<Account>null(empty)</Account>
			<Amount>' . $Amount . '</Amount>
			<TransDate>null(empty)</TransDate>
			<OrgTransID>' . $result[2] . '</OrgTransID>
			<DataSign>' . $signature . '</DataSign>
	     </RequestData>';
			$param['partnerCode'] = $partnerCode;
			$param['commandType'] = 'GetCard';
			$param['version'] = '1.0';
			$result = $client->call('RequestTransaction', $param);
			$result = self::Decrypt($result['RequestTransactionResult'], $triple_key);
	// Băm kết quả
			$result = explode('|', $result);
	// Băm lấy thông tin thẻ
			$result = explode(':', $result[2]);
			if ($result[0] && $result[1] && $result[3]) {
				return 'Ma the: ' . $result[0] . ' - Serial: ' . $result[1] . ' - Date: ' . $result[3];
			} else {
				return 'Không lấy được thông tin thẻ';
			}
		} else {
	// Giao dịch lỗi
			return self::errorType($result[0]);
		}
		
	}
	public static function export_excel($data = NULL, $title) {
		require_once (JPATH_COMPONENT.'/libs/PHPExcel.php');
		require_once (JPATH_COMPONENT.'/libs/PHPExcel/Writer/Excel2007.php');
		array_walk(
			$data,
			function (&$row) {
				$row = (array) $row;
			}
		);
		//array_merge($title,$data);
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Stock")
									->setLastModifiedBy("Stock")
									->setTitle("Office 2007 XLSX Test Document")
									->setSubject("Office 2007 XLSX Test Document")
									->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									->setKeywords("office 2007 openxml php")
									->setCategory("Test result file");
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->fromArray($data, null, 'A1');
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_end_clean();
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="Export_Code_"'.$title.'".xlsx"');
		$objWriter->save('php://output');
		exit;
	}	
	public static function get_number_of_codes_exported_to_onecard($voucher_id)
	{
		$db = JFactory::getDbo();

// Create a new query object.
		$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
		//$query = "SELECT SUM(number) FROM #__onecard_export_voucher_detail where voucher="
		$query->select('sum(' . $db->quoteName('number') . ')');
		$query->from($db->quoteName('#__onecard_export_voucher_detail'));
		$query->where($db->quoteName('voucher') . ' = ' . $voucher_id);

		$query->where($db->quoteName('is_onecard') . ' = 1');

// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$results = $db->loadResult();

		return ($results);
	}
	public static function get_voucher_detail ($voucher_id) {
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('*');
		$query->from($db->quoteName('#__onecard_voucher'));
		$query->where($db->quoteName('id') . ' = '. $voucher_id);
		

		
		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,1);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		Onecardhelper::log_sql("get_voucher_detail",$query->__toString());
		return ($results);
	}
	public static function get_voucher_price ($voucher_id, $partner_id) {
		$db = JFactory::getDbo();
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('price'));
		$query->from($db->quoteName('#__onecard_voucher_price'));
		$query->where($db->quoteName('voucher') . ' = '. $voucher_id);
		$query->where($db->quoteName('partner') . ' = '. $partner_id);
		$query->order('id DESC');
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,1);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		Onecardhelper::log_sql("get_voucher_price",$query->__toString());
		return ($results);
	}
	public static function check_voucher_export($export_id, $voucher_id){
		$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('id'));
	$query->from($db->quoteName('#__onecard_export_voucher_detail'));
	$query->where($db->quoteName('exported_id') . ' = '. $export_id);
	$query->where($db->quoteName('voucher') . ' = '. $voucher_id);
	

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadResult();
	Onecardhelper::log_sql("check_voucher_export",$query->__toString());
	return($results);
	}
	public static function get_type_name($id) {
		if ($id == 1)
			return "E-Voucher";
		elseif ($id == 2)
			return "C-Voucher";	
		elseif ($id == 3)
			return "Product";	
		elseif ($id == 4)
			return "Gift/Discount";	
		else	
			return "E-Voucher";	
	}
	public static function get_status_name($id) {
		if ($id == 1)
			return "Trong kho";
		elseif ($id == 2)
			return "Đã xuất";	
		elseif ($id == 3)
			return "Đã sử dụng";	
		elseif ($id == 4)
			return "Gift/Discount";	
		else	
			return "Trong kho";	
	}
	public static function gen_select ($table, $current_id=NULL, $related_field= NULL, $related_table = NULL, $fkey = NULL) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query->select(array('a.id','a.title'));
			$query->from($db->quoteName('#__'.$table, 'a'));
			if ($related_table && $fkey && $related_field) {
			$query->join('INNER', $db->quoteName('#__'.$related_table, 'b') . ' ON (' . $db->quoteName('a.'.$related_field) . ' = ' . $db->quoteName('b.id') . ')');
			$query->where($db->quoteName('b.id') . ' ='.$fkey);
		}
		$query->where($db->quoteName('a.state') . ' = 1');
			$query->order($db->quoteName('a.title') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();
		echo '<select name="'.$table.'[]"  multiple>
			<option value="">-- Lọc --</option>
		';
		$current_id = explode(",",$current_id);
		foreach ($results as $option) {
			$active = "";
			if (in_array($option->id,$current_id))
				$active = "selected";
			echo '<option value="'.$option->id.'" '.$active.'>'.$option->title.'</option>';
		}
		echo '</select>';	
	
		
	}
	public static function get_number_of_voucher ($voucher_id, $status=NULL, $price, $created = NULL, $expired = NULL) {
		
		// Get a db connection.
		$db = JFactory::getDbo();
	
		// Create a new query object.
		$query = $db->getQuery(true);
	
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('id');
		$query->from($db->quoteName('#__onecard_code'));
		if ($status) 
			$query->where($db->quoteName('status') . ' in ('. $status.')');
		$query->where($db->quoteName('voucher') . ' = '. $voucher_id);
		$query->where($db->quoteName('state') . ' = 1');
		$query->where($db->quoteName('input_price') . ' = '. $price);
		if ($created) {
			$query->where($db->quoteName('created') . ' = ' . $db->quote($created));
		}
		if ($expired) {
			$query->where($db->quoteName('expired') . ' = ' . $db->quote($expired));
		}
		//$query->order('ordering ASC');
	
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		//$results = $db->loadResult();
		//Onecardhelper::log_sql("get_number_of_voucher",$query->__toString());
		return ($num_rows);
	}
	public static function get_code_need_renew ($voucher_id) {
				// Get a db connection.
				$db = JFactory::getDbo();
		
				// Create a new query object.
				$query = $db->getQuery(true);
		
				// Select all records from the user profile table where key begins with "custom.".
				// Order it by the ordering field.
				$query->select('COUNT(*) as total, created, expired','input_price');
				$query->from($db->quoteName('#__onecard_code'));
				$query->where($db->quoteName('voucher') . ' = '. $voucher_id);
				$query->group($db->quoteName('created'));
				$db->setQuery($query);
				//Onecardhelper::log_sql("get_code_need_renew",$query->__toString());
				// Load the results as a list of stdClass objects (see later for more options on retrieving data).
				$results = $db->loadObjectlist();
				return ($results);
	}
	public static function doImport($fileExcel, $voucher_id, $type, $expired, $input_price){
  		require_once (JPATH_COMPONENT.'/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		
		$rows = $xlsx->rows() ;
		$user       = JFactory::getUser();
  		
		$count_insert=0;
		$duplicated="";
		$code_created = "";
		$code_imported = 0;
		foreach ($rows as $row) {
				if ($count_insert>0 && $row[0] != "") {
						
						$product[$count_insert] = new stdClass();						
				
						$product[$count_insert]->state = 1 ;	
												
						$product[$count_insert]->code = $row[0];
						$check_code = OnecardHelper::check_code($row[0]);
						$product[$count_insert]->barcode = $row[1];
						$product[$count_insert]->serial = $row[2];
						$product[$count_insert]->created_by = $user->id;
						$product[$count_insert]->voucher = $voucher_id;
						$product[$count_insert]->status = 1;
				$product[$count_insert]->input_price = $input_price;
						$product[$count_insert]->type = $type;
						$product[$count_insert]->expired = $expired;
						$product[$count_insert]->created = date("Y-m-d");
					if (!$check_code) {
						OnecardHelper::import_product($product[$count_insert],"onecard_code");
						
						$code_created.='"'.$row[0].'" ';
						$code_imported++;
					}else {
						$duplicated.= '"'.$row[0].'" ';
					}	
						//echo strtotime($row[2])."<br/>";
				}
			$count_insert++;		
						
		}
		
		$message = $code_imported.' code được tạo';
		if ($duplicated)
		$message.= " -|- Code trùng lặp: ".$duplicated;
		
		echo $message;

		
			
		


		
}
	public static function import_product($item,$table) {
		$result = JFactory::getDbo()->insertObject('#__'.$table, $item);
	}
	public static function get_max_id($table_name){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__'.$table_name));
	
		$query->order('id DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,1);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		Onecardhelper::log_sql("get_max_id",$query->__toString());
		if ($results) {
			$results++;
			return ($results);
		}
			
		else {
			return 1;
		}
			
	} 



	public static function export_codes ($merchant, $value, $expired, $number){
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select(array('a.code','a.barcode','a.id','a.serial'))
			->from($db->quoteName('#__onecard_code', 'a'))
			->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')')
			->where($db->quoteName('b.brand') . ' = '.$merchant)
			->where($db->quoteName('b.value') . ' = '.$db->quote($value))
			->where($db->quoteName('a.expired') . ' >= '.$db->quote($expired))
			->where($db->quoteName('a.status') . ' = 1')
			->where($db->quoteName('a.state') . ' = 1')
			->order($db->quoteName('a.expired') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadObjectList();	
		Onecardhelper::log_sql("export_codes",$query->__toString());
		return ($exported);
	}
	public static function export_codes_by_voucher ($voucher, $expired, $number, $actived_code ){
		$voucher_detail = OnecardHelper::get_voucher_detail($voucher);
		$voucher_type = $voucher_detail->type;
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select(array('a.code', 'a.barcode', 'a.id', 'a.serial', 'eventoc_export', 'merchantoc','b.value','b.type'))
			->from($db->quoteName('#__onecard_code', 'a'))
			->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')')
			->join('INNER', $db->quoteName('#__onecard_brand', 'br') . ' ON (' . $db->quoteName('b.brand') . ' = ' . $db->quoteName('br.id') . ')')
			//->join('LEFT', $db->quoteName('#__onecard_voucher_event', 'e') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('e.voucher') . ')')
			->where($db->quoteName('a.voucher') . ' = '.$voucher);
			if ($voucher_type != 3) {
				$query->where($db->quoteName('a.expired') . ' >= '.$db->quote($expired));
			}
			if (is_array($actived_code) && $actived_code){
			$actived_code = implode(",", $actived_code);
				$query->where($db->quoteName('a.id') . ' not in ('. $actived_code .')');
			}
			$query->where($db->quoteName('a.status') . ' = 1');
		$query->where($db->quoteName('a.state') . ' = 1')

			->order($db->quoteName('a.expired') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadObjectList();	
		Onecardhelper::log_sql("export_codes_by_voucher",$query->__toString());
		return ($exported);

	}
	public static function get_voucher_id ($brand_id, $value) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__onecard_voucher'));
		
		
		$query->where($db->quoteName('brand') . ' = '. $brand_id);
		$query->where($db->quoteName('value') . ' = '. $value);
		$query->order($db->quoteName('expired') . ' ASC');
		$db->setQuery($query,0,1);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		Onecardhelper::log_sql("get_voucher_id",$query->__toString());
		return ($results);
	}
	
	
	
	

	public static function get_merchant_name ($id) {
		// Get a db connection. 
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('title'));
		$query->from($db->quoteName('#__onecard_brand'));
		$query->where($db->quoteName('id') . ' = '. $id);
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		Onecardhelper::log_sql("get_merchant_name", $query->__toString());
		return ($results);
	}
	
	
	
	
	
	
	public static function check_code ($code) {
	

			// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__onecard_code'));
			$query->where($db->quoteName('code') . ' = '. $db->quote($code));
			

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadResult();
			Onecardhelper::log_sql("check_code",$query->__toString());
			return ($results);
	} 
	public function get_export_detail($voucher_id, $exported_id) {
				$db = JFactory::getDbo();
		
				// Create a new query object.
				$query = $db->getQuery(true);
		
				// Select all records from the user profile table where key begins with "custom.".
				// Order it by the ordering field.
				$query->select($db->quoteName('exported_code'));
				$query->from($db->quoteName('#__onecard_export_voucher_detail'));
				$query->where($db->quoteName('exported_id') . ' = '.$exported_id);
				$query->where($db->quoteName('voucher') . ' = '.$voucher_id);
				$db->setQuery($query);
				$detail = $db->loadResult();
				Onecardhelper::log_sql("get_export_detail",$query->__toString());
				return $detail;
	}
	public static function get_event_oc_id ($voucher_id) {
		$db = JFactory::getDbo();
		
				// Create a new query object.
		$query = $db->getQuery(true);
		
				// Select all records from the user profile table where key begins with "custom.".
				// Order it by the ordering field.
		$query->select($db->quoteName('eventoc_export'));
		$query->from($db->quoteName('#__onecard_voucher'));
		
		$query->where($db->quoteName('id') . ' = ' . $voucher_id);
		//$query->order($db->quoteName('event') . ' DESC');

		$db->setQuery($query);
		$detail = $db->loadResult();
		Onecardhelper::log_sql("get_event_oc_id", $query->__toString());
		return $detail;
	}
	public static function get_merchant_oc_id ($brand_id) {
		$db = JFactory::getDbo();
		
				// Create a new query object.
		$query = $db->getQuery(true);
		
				// Select all records from the user profile table where key begins with "custom.".
				// Order it by the ordering field.
		$query->select($db->quoteName('merchantoc'));
		$query->from($db->quoteName('#__onecard_brand'));

		$query->where($db->quoteName('id') . ' = ' . $brand_id);
		//$query->order($db->quoteName('event') . ' DESC');

		$db->setQuery($query);
		$detail = $db->loadResult();
		Onecardhelper::log_sql("get_merchant_oc_id", $query->__toString());
		return $detail;
	}
	public function change_code_status ($export_id){
		// Get a db connection.
			$db = JFactory::getDbo();
		
				// Create a new query object.
				$query = $db->getQuery(true);
		
				// Select all records from the user profile table where key begins with "custom.".
				// Order it by the ordering field.
				$query->select($db->quoteName(array('id','voucher','number','expired','exported_id','exported_code','price')));
				$query->from($db->quoteName('#__onecard_export_voucher_detail'));
				$query->where($db->quoteName('exported_id') . ' = '.$export_id);
				$query->where($db->quoteName('exported_code') . ' = 0');
				$db->setQuery($query);
				$details = $db->loadObjectList();
				$post_code = array();
				foreach ($details as $detail) {
					$exported_code = OnecardHelper::export_codes_by_voucher($detail->voucher, $detail->expired, $detail->number, NULL);
					if ($detail->number > count($exported_code)) {
							echo $detail->id."NO<br/>";
					}else { // OK
							// Change status
							$need_change_status = array();
							foreach ($exported_code as $item) {
								$voucher_detail = self::get_voucher_detail($detail->voucher);
								$post_code[] = array(
									'coupon' => $item->code,
									'pincode' => $item->serial,
									'event_id' => self::get_event_oc_id($detail->voucher),
									'status' => 1,
									'created' => strtotime(date('Y-m-d 23:59:59')),
									'merchant_id' => self::get_merchant_oc_id($voucher_detail->brand),
									'end_time' => strtotime($detail->expired. " 16:59:59"),
									'item_id' => $export_id,
									'price' => $voucher_detail->value,
									'cart_detail_id' => 82,
									'customer_id' => 1,
									'note'=>''
								);
					$need_change_status[] = $item->id;
								//$result_post = self::postCurl('https://onecard.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
							//	Onecardhelper::log_sql("post_url". $item, json_encode($post_code));
							}
							if (!$detail->price) {
								$detail->price = $voucher_detail->value;
							}
							$update_code = implode(",", $need_change_status);
							//$update_code = str_replace(',','","', $code);
							//$update_code = '"'. $update_code.'"';
							$db = JFactory::getDbo();			
							$query = $db->getQuery(true);
							$fields = array(
								$db->quoteName('status') . ' = 2',
								$db->quoteName('virtual_code') . ' = 0',
								$db->quoteName('exported_id') . ' = '.$export_id,
								$db->quoteName('exported_detail_id') . ' = '.$detail->id,
								$db->quoteName('export_price') . ' = ' . $detail->price
							);
							$conditions = array(
								$db->quoteName('id') . ' IN ' . ' (' . $update_code .')'
							);
							$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);
						//	Onecardhelper::log_sql("change_code_status", $query->__toString());
							$db->setQuery($query);			
							$result = $db->execute();

							// Update detail status
							$detail->exported_code = 1;
							$update_detail = JFactory::getDbo()->updateObject('#__onecard_export_voucher_detail', $detail, 'id');
					}

				}
		$result_post = self::postCurl('https://onecard.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
		Onecardhelper::log_sql("post_code_". $export_id, json_encode($post_code));
	}
	
	public static function log_sql ($function_name, $query) {
		$profile = new stdClass();
		$profile->function = $function_name;
		$profile->query=$query;
		
		
		// Insert the object into the user profile table.
		$result = JFactory::getDbo()->insertObject('#__onecard_log_function', $profile);
	}
}
