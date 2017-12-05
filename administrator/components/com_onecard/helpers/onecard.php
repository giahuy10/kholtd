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
			$query->where($db->quoteName('status') . ' = '. $status);
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
		$results = $db->loadResult();
		Onecardhelper::log_sql("get_number_of_voucher",$query->__toString());
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
			->order($db->quoteName('a.expired') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadObjectList();	
		Onecardhelper::log_sql("export_codes",$query->__toString());
		return ($exported);
	}
	public static function export_codes_by_voucher ($voucher, $expired, $number){
		$voucher_detail = OnecardHelper::get_voucher_detail($voucher);
		$voucher_type = $voucher_detail->type;
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select($db->quoteName('a.code'))
			->from($db->quoteName('#__onecard_code', 'a'))
			->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')')
			
			->where($db->quoteName('a.voucher') . ' = '.$voucher);
			if ($voucher_type != 3) {
				$query->where($db->quoteName('a.expired') . ' >= '.$db->quote($expired));
			}
			
		$query->where($db->quoteName('a.status') . ' = 1')
			->order($db->quoteName('a.expired') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadColumn();	
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
					$exported_code = OnecardHelper::export_codes_by_voucher($detail->voucher, $detail->expired, $detail->number);
					if ($detail->number > count($exported_code)) {
							echo $detail->id."NO<br/>";
					}else { // OK
							// Change status
							foreach ($exported_code as $item) {
								$voucher_detail = self::get_voucher_detail($detail->voucher);
								$post_code[] = array(
									'coupon' => $item,
									'event_id' => self::get_event_oc_id($detail->voucher),
									'status' => 1,
									'created' => strtotime(date('Y-m-d 23:59:59')),
									'merchant_id' => self::get_merchant_oc_id($voucher_detail->brand),
									'end_time' => strtotime($detail->expired),
									'price' => $voucher_detail->value,
									'cart_detail_id' => 82,
									'customer_id' => 1
								);
								//$result_post = self::postCurl('https://onecard.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
							//	Onecardhelper::log_sql("post_url". $item, json_encode($post_code));
							}
							if (!$detail->price) {
								$detail->price = $voucher_detail->value;
							}
							$code = implode(",",$exported_code);
							$update_code = str_replace(',','","', $code);
							$update_code = '"'. $update_code.'"';
							$db = JFactory::getDbo();			
							$query = $db->getQuery(true);
							$fields = array(
								$db->quoteName('status') . ' = 2',
								$db->quoteName('exported_id') . ' = '.$export_id,
								$db->quoteName('exported_detail_id') . ' = '.$detail->id,
								$db->quoteName('export_price') . ' = ' . $detail->price
							);
							$conditions = array(
								$db->quoteName('code') . ' IN ' . ' (' . $update_code .')'
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
		Onecardhelper::log_sql("post_url", $result_post);
	}
	
	public static function log_sql ($function_name, $query) {
		$profile = new stdClass();
		$profile->function = $function_name;
		$profile->query=$query;
		
		
		// Insert the object into the user profile table.
		$result = JFactory::getDbo()->insertObject('#__onecard_log_function', $profile);
	}
}
