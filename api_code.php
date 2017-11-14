<?php
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

function check_code($codes, $type, $merchantoc, $used_location = NULL) {
	$brand_id = get_brand_id($merchantoc);
	foreach ($codes as $code) {
		$db = JFactory::getDbo();	
		// Create a new query object.
		$query = $db->getQuery(true);	
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('a.id', 'code', 'barcode', 'serial','a.status','b.title','a.voucher','a.exported_id','b.brand')));
		$query->from($db->quoteName('#__onecard_code','a'));
		$query->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')');
		//$query->join('INNER', $db->quoteName('#__onecard_brand', 'c') . ' ON (' . $db->quoteName('b.brand') . ' = ' . $db->quoteName('c.id') . ')');
		$query->where($db->quoteName('code') . ' = '. $db->quote($code));
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$result = $db->loadObject();
		
		if ($result) {
			if ($result->brand != $brand_id) {
				$response->status = -1;
				$response->message = "Ma code ".$code." khong duoc ap dung tai dia diem nay";
				$response->data = NULL;
			}elseif ($result->status == 3) {
				$response->status = -1;
				$response->message = "Ma code ".$code." da duoc su dung";
				$response->data = NULL;
			}elseif (!$result->exported_id) {
				$response->status = -1;
				$response->message = "Ma code ".$code." chua duoc ban";
				$response->data = NULL;
			}elseif (strtotime(get_expired($result->voucher, $result->exported_id)) < strtotime(date("Y-m-d"))){
				$response->status = -1;
				$response->message = "Ma code ".$code." da het han su dung";
				$response->data = NULL;
			}else {
				$response->status = 1;
				$response->message = "Ma code ".$code." hop le! ";
				if ($type == "active") {
					$response->message .= "Kich hoat thanh cong!";
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
		}else {
			$response->status = -1;
			$response->message = "Khong tim thay ma code ".$code;
			$response->data = NULL;
		}
	}
	$response->status = 1;
	$response->message = "Success";
	$response->data = $data;
	return $response;
}
function get_voucher_id ($eventoc) {
	$db = JFactory::getDbo();	
	// Create a new query object.
	$query = $db->getQuery(true);	
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('id'));
	$query->from($db->quoteName('#__onecard_voucher'));

	$query->where($db->quoteName('eventoc') . ' = '. $eventoc);
	// Reset the query using our newly populated query object.
	$db->setQuery($query);
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
	$result = $db->loadResult();
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
function export_codes_by_eventoc ($data){
	/* JSON
			{
				"order": {
					"id":"1",
					"cart_code":"031102001"
				}
				"items": {
					"0": {"id":"241","expired":"2017-11-11","quantity":"2"},
					"1": {"id":"242","expired":"2017-11-11","quantity":"2"}
				}
				
			
			}	
	 */	
	$order = $data->order;
	$items = $data->items;
		

	// CReate EXPORT
	
	$export = new stdClass();
	$export->id = get_max_id("onecard_export_voucher");
	$export->order_number = $order->cart_code;
	$export->order_id = $order->id;
	$import_export = JFactory::getDbo()->insertObject('#__onecard_export_voucher', $export);		

	// CREATE EXPORT DETAIL
	
	foreach ($items as $key => $item) {
		// Create and populate an object.
		$export_detail[$key] = new stdClass();
		$export_detail[$key]->voucher = get_voucher_id($item->id);
		$export_detail[$key]->number = $item->quantity;
		$export_detail[$key]->expired = $item->expired;
		$export_detail[$key]->exported_id = $export->id;
		$export_detail[$key]->exported_code = 1;
		$import_export_detail = JFactory::getDbo()->insertObject('#__onecard_export_voucher_detail', $export_detail[$key]);
		$codes[$item->id] = get_codes($export_detail[$key]->voucher, $export_detail[$key]->expired, $export_detail[$key]->number);	
		//$item->codes = $codes[$key];
		foreach ($codes[$item->id] as $code) {
			$code->status = 2;
			$code->exported_id = $export->id;
			$update_code_status = JFactory::getDbo()->updateObject('#__onecard_code', $object, 'id');
		}
	}
	
		$response->status = 1;
		$response->message = "Success";
		$response->data = $codes;
	
	return $response;
}
function get_number_of_codes($voucher_id) {
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
	$num_rows = $db->getNumRows();
	return $num_rows;
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
		$merchant_id = $data->merchant;
		$codes = $data->codes;
		$codes = str_replace(" ","",$codes);
		$codes = explode(",",$codes);
		$response = check_code($codes,"check",$merchant_id);

	
		break;
	case "active":
		
		$merchant_id = $data->merchant;
		$codes = $data->codes;
		$codes = str_replace(" ","",$codes);
		$codes = explode(",",$codes);
		$used_location = $data->active_location;
		$response = check_code($codes,"active",$merchant_id,$used_location);
		
		
		break;
	case "get_number":
		$event_id = JRequest::getVar('id');
		$voucher_id = get_voucher_id($event_id);
		if ($voucher_id) {
			$response['status'] = 1;
			$response['message'] = "Success";
			$response['data'] = get_number_of_codes($voucher_id);
		}else {
			$response['status'] = -1;
			$response['message'] = "Error: Không tìm thấy sự kiện";
			$response['data'] = NULL;
		}
		
	
	case "get": 		
			$response = export_codes_by_eventoc($data);
			
		break;
	default:
		echo "ok";			
}
echo json_encode($response);
