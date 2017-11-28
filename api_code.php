<?php
header('Content-Type: application/json');
$task = JRequest::getVar('task');

$data = file_get_contents("php://input");
$data = json_decode($data);
function get_expired ($voucher, $exported_id) {
	// Get a db connection.
	$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('expired'));
	$query->from($db->quoteName('#__onecard_export_voucher_detail'));
	$query->where($db->quoteName('voucher') . ' = '. $voucher);
	$query->where($db->quoteName('exported_id') . ' = '. $exported_id);
	$query->order('id DESC');

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadResult();
	return $results;
}
function active_code ($code) {
	$db = JFactory::getDbo();

	$query = $db->getQuery(true);

// Fields to update.
	$fields = array(
		$db->quoteName('status') . ' = 3' ,
		$db->quoteName('used_date') . ' = '. $db->quote(date("Y-m-d H:i:s"))
	);

// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('code') . ' = ' . $db->quote($code)
	);

	$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);

	$db->setQuery($query);

	$result = $db->execute();
	return $result;
	
}
function check_code_oo($codes, $type, $merchantoc, $used_location = NULL) {
	return $codes;
}
function check_code($codes, $type, $merchantoc, $used_location = NULL) {
	$brand_id = get_brand_id($merchantoc);
	
	foreach ($codes as $code) {
		$db = JFactory::getDbo();	
		$query = $db->getQuery(true);	
		$query->select($db->quoteName(array('a.id', 'code', 'barcode', 'serial','a.status','b.title','a.voucher','a.exported_id','b.brand','b.value','b.value')));
		$query->select('b.id as voucher_id');
		$query->from($db->quoteName('#__onecard_code','a'));
		$query->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')');
		$query->where($db->quoteName('code') . ' = '. $db->quote($code));
		$db->setQuery($query);
		$result = $db->loadObject();
		//$result['$code'] = $query->__toString();
		
		if ($result) {
			if ($result->brand != 55 && !in_array($result->brand,$brand_id)) {
				$response[$code]->status = -1;
				$response[$code]->message = "Mã code " . $code . " không áp dụng tại địa điểm này";
				$couponError[$code] = array('dealCode' => $code, 'msg' => 'Mã code ' . $code . ' không áp dụng tại địa điểm này ');

			} elseif ($result->status == 3) {
				$response[$code]->status = -1;
				$response[$code]->message = "Mã code " . $code . " đã được sử dụng";
				$couponError[$code] = array('dealCode' => $code, 'msg' => 'Mã code ' . $code . ' đã được sử dụng');
				$response->data = NULL;
			} elseif (!$result->exported_id) {
				$response[$code]->status = -1;
				$response[$code]->message = "Mã code " . $code . " chưa được bán";
				$couponError[$code] = array('dealCode' => $code, 'msg' => 'Mã code ' . $code . ' chưa được bán');
				$response->data = NULL;
			} elseif (strtotime(get_expired($result->voucher, $result->exported_id)) < strtotime(date("Y-m-d"))) {
				$response[$code]->status = -1;
				$response[$code]->message = "Mã code " . $code . " đã hết hạn sử dụng";
				$couponError[$code] = array('dealCode' => $code, 'msg' => 'Mã code ' . $code . ' đã hết hạn sử dụng');
				$response->data = NULL;
			} else {
				$response[$code]->status = 1;
				$response[$code]->message = "Mã code " . $code . " hợp lệ! ";
				$allDiscount += $result->value;
				$arrCoupons[$result->voucher_id]['total'] = $result->value;
				$arrCoupons[$result->voucher_id]['total_f'] = number_format($result->value);
				if ($type == "active") {
					$response[$code]->message .= "Kích hoạt thành công!";
					$object = new stdClass();
					// Must be a valid primary key value.
					$object->id = $result->id;
					$object->status = 3;
					$object->used_date = date("Y-m-d H:i:s");
					$object->used_location = $used_location;
					// Update their details in the users table using id as the primary key.
					$active_code = JFactory::getDbo()->updateObject('#__onecard_code', $object, 'id');
				}
				$response->data = $result;
			}
		} else {
			$response[$code]->status = -1;
			$response[$code]->message = "Không tìm thấy mã code " . $code;
			$response[$code]->data = NULL;
			$couponError[$code] = array('dealCode' => $code, 'msg' => 'Không tìm thấy mã code ' . $code);
		}
		

	}
	return array('couponError' => $couponError, 'allDiscount' => $allDiscount, 'arrCoupons' => $arrCoupons);
	//return $codes;
}
function get_voucher_type($voucher_id)
{
	$db = JFactory::getDbo();	
	// Create a new query object.
	$query = $db->getQuery(true);	
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('type'));
	$query->from($db->quoteName('#__onecard_voucher'));

	$query->where($db->quoteName('id') . ' = ' . $voucher_id);
	// Reset the query using our newly populated query object.
	$db->setQuery($query);
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$result = $db->loadResult();
	return ($result);
}
function get_voucher_id ($eventoc) {
	$db = JFactory::getDbo();	
	// Create a new query object.
	$query = $db->getQuery(true);	
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('voucher'));
	$query->from($db->quoteName('#__onecard_voucher_event','a'));
	$query->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')');
	$query->where($db->quoteName('event') . ' = '. $eventoc);
	$query->where($db->quoteName('b.state') . ' = 1');
	// Reset the query using our newly populated query object.
	$query->order($db->quoteName('voucher') . ' DESC');
	$db->setQuery($query,0,1);
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$result = $db->loadResult();
	return ($result);
}
function get_brand_id ($merchantoc) {
	$db = JFactory::getDbo();	
	// Create a new query object.
	$query = $db->getQuery(true);	
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('id'));
	$query->from($db->quoteName('#__onecard_brand'));

	$query->where($db->quoteName('merchantoc') . ' = '. $merchantoc);
	// Reset the query using our newly populated query object.
	$db->setQuery($query);
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$result = $db->loadColumn();
	return ($result);
}
function get_max_id ($table){
	$db = JFactory::getDbo();	
	// Create a new query object.
	$query = $db->getQuery(true);	
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('id'));
	$query->from($db->quoteName('#__'.$table));
	$query->order($db->quoteName('id') . ' DESC');
	$db->setQuery($query,0,1);
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$result = $db->loadResult();
	$result++;
	return ($result);
}
function get_codes ($voucher, $expired, $number){
	
	$db = JFactory::getDbo();

	
		// Create a new query object.
	$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
	$query
		->select(array('a.code', 'a.barcode', 'a.serial','a.id'))
		->from($db->quoteName('#__onecard_code', 'a'))
		->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')')

		->where($db->quoteName('a.voucher') . ' = ' . $voucher)
		->where($db->quoteName('a.expired') . ' >= ' . $db->quote($expired))
		->where($db->quoteName('a.status') . ' = 1')
		->where($db->quoteName('a.state') . ' = 1')
		->where($db->quoteName('b.state') . ' = 1')
		->order($db->quoteName('a.expired') . ' ASC');

		// Reset the query using our newly populated query object.
	$db->setQuery($query, 0, $number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$exported = $db->loadObjectlist();
	return $exported;
}
function sendSMS ($Content, $YourPhone) {
	$APIKey = "2A00924E0B265978F73EB9B28088DF";
	$SecretKey = "C60751C63C7740DCD5F0886E3DCA18";
	
	$SendContent = urlencode($Content);
	$data = "http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$YourPhone&ApiKey=$APIKey&SecretKey=$SecretKey&Content=$SendContent&SmsType=2&Brandname=ONECARD";

	$curl = curl_init($data);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);

	$obj = json_decode($result, true);
	return $obj;
}
function stripUnicode($str = '')
	{
		if ($str != '') {
			$marTViet = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ", "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ", "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ", "ỳ", "ý", "ỵ", "ỷ", "ỹ", "đ", "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ", "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ", "Ì", "Í", "Ị", "Ỉ", "Ĩ", "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ", "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ", "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ", "Đ");
			$marKoDau = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D");
			$str = str_replace($marTViet, $marKoDau, $str);
		}
		return $str;
	}
function postCurl($url, $var)
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
function export_codes_by_eventoc ($data){
	
	$order = $data->cart;
	$items = $data->orders;
		
	//var_dump($data);		
	// CReate EXPORT
	
	$export = new stdClass();
	$export->id = get_max_id("onecard_export_voucher");
	$export->order_number = $order->cart_code;
	$export->state = 1;
	$export->event = 4;
	$export->created = date("Y-m-d H:i:s");
	$export->order_id = $order->id;
	$export->note = "Đơn hàng mã ". $order->cart_code;
	$import_export = JFactory::getDbo()->insertObject('#__onecard_export_voucher', $export);		

	// CREATE EXPORT DETAIL

	$json_data = "{";
	
		
	$i = 0;
	$sms_return = array();
	$post_code = array();
	foreach ($items as $key => $item) {
		
		// Create and populate an object.
		$export_detail[$key] = new stdClass();
		$export_detail[$key]->voucher = get_voucher_id($item->event->id);
		$export_detail[$key]->number = $item->quantity;
		$export_detail[$key]->price = $item->price;
		if ($item->event->exprie_type > 0) {
			$date = date("Y-m-d");// current date
			$exchange_date = " +" . $item->event->exprie_type . " day";
			$date2 = strtotime(date("Y-m-d", strtotime($date)) . $exchange_date);
			$export_detail[$key]->expired = date("Y-m-d", $date2);
			$expired_number = $item->event->exprie_type;
			$exp_time = strtotime(date('Y-m-d 23:59:59', time() + ($item->event->exprie_type * 86400)));
			 
		}else{
			$export_detail[$key]->expired = date("Y-m-d",$item->event->expried);
			$now = date("Y-m-d"); // or your date as well
			$startTimeStamp = strtotime($now);
			$endTimeStamp = strtotime($export_detail[$key]->expired);
			
			$timeDiff = abs($endTimeStamp - $startTimeStamp);

			$numberDays = $timeDiff / 86400;  // 86400 seconds in one day

// and you might want to convert to integer
			$expired_number = intval($numberDays);
			//$export_detail[$key]->price = $item->event->expired;
			$exp_time = $item->event->expried;
		}
		
		$export_detail[$key]->exported_id = $export->id;
		$export_detail[$key]->exported_code = 1;
		$export_detail[$key]->is_onecard = 1;
		$json_array[$i]= '"list_templates'.$i.'" : {"voucher" :"'.$export_detail[$key]->voucher.'" , "price" :"'. $export_detail[$key]->price .'" , "number" :"'. $export_detail[$key]->number .'" , "expired" : "'. $expired_number .'"}';
		$i++;
		
		$import_export_detail = JFactory::getDbo()->insertObject('#__onecard_export_voucher_detail', $export_detail[$key]);
		$codes[$item->event->id] = get_codes($export_detail[$key]->voucher, $export_detail[$key]->expired, $export_detail[$key]->number);	
		//$item->codes = $codes[$key];
		$voucher_type = get_voucher_type($export_detail[$key]->voucher);
		$sms_code = "";
		
		foreach ($codes[$item->event->id] as $code) {
			$post_code[] = array(
				'coupon'=>$code->code,
				'event_id'=> $item->event->id,
				'status'=>1,
				'created'=> strtotime(date('Y-m-d 23:59:59')),
				'merchant_id'=>$item->event->merchant_id,
				'end_time'=> $exp_time,
				'price'=>$item->event->price,
				'cart_detail_id'=>$key,
				'customer_id'=>$order->customer_id
			);
			//postCurl('https://onecard.ycar.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
			$sms_code.= $code->code." ";
			$code->status = 2;
			$code->exported_id = $export->id;
			$code->export_price = $item->event->price;
			$update_code_status = JFactory::getDbo()->updateObject('#__onecard_code', $code, 'id');
		}
		if ($voucher_type == 1) {
			$content = "Evoucher ".$sms_code. "- ".number_format($item->price)." vnd - HSD: ". date('d/m/Y',strtotime($export_detail[$key]->expired)) ." Tai ". stripUnicode($item->event->title)." - Hotline:19001748";
		} else {
			$content = "Voucher ". stripUnicode($item->event->title) . " - " . number_format($item->price) . " vnd - HSD: " . date('d/m/Y', strtotime($export_detail[$key]->expired)) . " se duoc chuyen den dia chi cua quy khach - Hotline:19001748";
		}
		//$sms_return[$key] = sendSMS($content, $order->phone);
	}
	$json_text = implode(",", $json_array);
	$json_data .= $json_text;
	$json_data .= "}";
	$export->list_templates = $json_data;
	$update_export = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $export, 'id');
		$response->status = 1;
		$response->message = "Success 6";
		$response->data = array("code"=> $post_code);
	
	
	return $response;
}
function get_voucher_detail ($voucher_id) {
	$db = JFactory::getDbo();
		
		// Create a new query object.
	$query = $db->getQuery(true);
		
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
	$query->select('*');
	$query->from($db->quoteName('#__onecard_voucher'));
	$query->where($db->quoteName('id') . ' = ' . $voucher_id);
		

		
		// Reset the query using our newly populated query object.
	$db->setQuery($query, 0, 1);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadObject();
	
	return ($results);
}
function get_number_of_codes($voucher_id, $max_sell, $current_quan) {
	// Get a db connection.
	$db = JFactory::getDbo();

// Create a new query object.
	$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
	$query->select($db->quoteName('id'));
	$query->from($db->quoteName('#__onecard_code'));
	$query->where($db->quoteName('voucher') . ' = ' . $voucher_id);
	$query->where($db->quoteName('status') . ' = 1');
	$query->where($db->quoteName('state') . ' = 1');

// Reset the query using our newly populated query object.
	$db->setQuery($query);
	$db->execute();
	$available_code = $db->getNumRows();
	$detail = get_voucher_detail($voucher_id);
	$max_code = $max_sell;
	if ($max_code > 0) {
		$exported_oc = get_number_of_codes_exported_to_onecard($voucher_id);
		$available_code_oc = $max_code - $exported_oc;
		if ($available_code_oc > $available_code){
			return $available_code - $current_quan;
		}else {
			return $available_code_oc - $current_quan;
		}
	}else {
		return $available_code - $current_quan;
	}
}
function get_number_of_codes_exported_to_onecard($voucher_id) {
	$db = JFactory::getDbo();

// Create a new query object.
	$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
	$query->select('sum(' . $db->quoteName('number') . ')');
	$query->from($db->quoteName('#__onecard_export_voucher_detail'));
	$query->where($db->quoteName('voucher') . ' = ' . $voucher_id);
	
	$query->where($db->quoteName('is_onecard') . ' = 1');

// Reset the query using our newly populated query object.
	$db->setQuery($query);
	$results = $db->loadResult();

	return ($results);
}
function log_api($act, $task, $data,$response) {
	$profile = new stdClass();
	$profile->act = $act;
	$profile->task = $task;
	$profile->value = json_encode($data);
	$profile->response = json_encode($response);
	//$profile->ordering = 1;

// Insert the object into the user profile table.
	$result = JFactory::getDbo()->insertObject('#__onecard_log_api', $profile);
}
function objToArray($obj, &$arr)
{

	if (!is_object($obj) && !is_array($obj)) {
		$arr = $obj;
		return $arr;
	}

	foreach ($obj as $key => $value) {
		if (!empty($value)) {
			$arr[$key] = array();
			objToArray($value, $arr[$key]);
		} else {
			$arr[$key] = $value;
		}
	}
	return $arr;
}
function get_quantity ($data) {
	$response = array();
	foreach ($data as $x=>&$item) {
		$voucher_id = get_voucher_id($item->id);
		if ($voucher_id) {
			$item->quantity = get_number_of_codes($voucher_id, $item->max_sell,0);
			$response[$x]->voucher = $voucher_id;
		}
		$response[$x] = $item;
		$response[$x]->test = 0;
	}
	return $response;
}
//$response = array();
switch ($task) {

	/* JSON 
	
		[
			"merchant_id": "24",
			"codes": "code1, code2, code3",
			"active_location":"location_id"

		]
	*/
	case "check":
		$merchant_id = $data->merchant_id;
		$codes = $data->codes;
		$codes = str_replace(" ","",$codes);
		$codes = explode(",",$codes);
		$response_check = check_code($codes,"check",$merchant_id);
		$response['status'] = 1;
		$response['message'] = "Success";
		$response['data'] = $response_check;
		
		log_api("code", "check", $data, json_encode($response));	
	
		break;
	case "active":
		
		$code = $data->code;
		$response = active_code($code);
		log_api("code", "active", $data, $response);
		
		break;
	case "number":
		$event_id = $data->event_id;
		$max_sell = $data->max_sell;
		$cart = $data->cart;
		$type = $data->type;
		$current_quan = 0;
		if ($cart->$event_id && !$type) {
			$current_quan = $cart->$event_id->quan;
		}
		
		$voucher_id = get_voucher_id($event_id);
		
		if ($voucher_id) {
			$response['status'] = 1;
			$response['message'] = "Success";
			$response['data'] = get_number_of_codes($voucher_id, $max_sell, $current_quan);
		}else {
			$response['status'] = -1;
			$response['message'] = "Error: Không tìm thấy sự kiện";
			$response['data'] = NULL;
		}
		log_api("code", "number", $data, $response);
		break;
	case "test":
	
		log_api("code", "test", $data, $response);
		break;	
	case "getquantity":
		$response = get_quantity($data);
		break;
	case "get": 		
			$response = export_codes_by_eventoc($data);
			log_api("code", "get", $data, $response);
			
		break;
	default:
		echo "ok";			
}
echo json_encode($response);
