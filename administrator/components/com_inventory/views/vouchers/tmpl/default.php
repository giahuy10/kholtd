<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Inventory
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport( 'joomla.form.form' );
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_inventory/assets/css/inventory.css');
$document->addStyleSheet(JUri::root() . 'media/com_inventory/css/list.css');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<?php 
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_inventory');
$saveOrder = $listOrder == 'a.`ordering`';
$session = JFactory::getSession();
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_inventory&task=vouchers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'voucherList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
$query->select($db->quoteName(array('id', 'title')));
$query->from($db->quoteName('#__categories'));
$query->where($db->quoteName('extension') . ' = '. $db->quote('com_inventory'));
$query->where($db->quoteName('published') . ' = 1');


// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$merchants = InventoryHelper::get_merchants("com_inventory");
$events = InventoryHelper::get_events ();
$partners = InventoryHelper::get_merchants("com_content");

$sortFields = $this->getSortFields();
$jinput = JFactory::getApplication()->input;
$type = $jinput->get('type',0);
$merchant = $jinput->get('merchant',0);
$sheet = $jinput->get('sheet',1);
if ($type == 1) { // UPLOAD FILE
		$value = $jinput->get('value',0);
		$price = $jinput->get('price',0);
	
		$event = $jinput->get('event',"",string);
		$expired = $jinput->get('expired',"");
		$target_dir = JPATH_ROOT.'/images/import/';
		$target_file = $target_dir . date('m-d-Y_his').basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<p class='text-danger'>Sorry, file already exists.</p>";
			$uploadOk = 0;
		}


		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<p class='text-danger'>Sorry, your file was not uploaded.</p>";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			   // echo "<p class='text-success'>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
			   
			   if($imageFileType == "xlsx") {
				   $max_uploaded_id = InventoryHelper::get_max_id('inventory_code_created');
				   doImport($target_file, $merchant, $sheet, $max_uploaded_id,$userId,$price,$event,$value,$expired);
				   
			   }else {
				   //doImportxls($target_file);
			   }
				
						
				/*if (!unlink($target_file))
					{
				  echo ("Error deleting $target_file");
				  }
				else
				  {
				  //echo ("<p class='text-info'>Deleted $target_file</p>");
				}*/
			} else {
				echo "<p class='text-danger'>Sorry, there was an error uploading your file.</p>";
			}
		}
}
if ($type == 2) { // TẠO MÃ
	$number = $jinput->get('number',0);
	$value = $jinput->get('value',0);
	$price = $jinput->get('price',0);
	$event_code = $jinput->get('event_code',0);
	$event = $jinput->get('event',"",string);
	$expired = JRequest::getVar('expired');
	$k= 0;
	$max_created_id = InventoryHelper::get_max_id('inventory_code_created');
	$inventory_code_created = new stdClass();
	$inventory_code_created->id = $max_created_id;
	$inventory_code_created->number = $number;
	$inventory_code_created->price = $price;
	$inventory_code_created->value = $value;
	$inventory_code_created->event_code = $event_code;
	$inventory_code_created->event = $event;
	$inventory_code_created->expired = $expired;
	$inventory_code_created->merchent = $merchant;
	$inventory_code_created->user_id = $userId;
	$inventory_code_created->type = $type;
	$insert_code_exported = JFactory::getDbo()->insertObject('#__inventory_code_created', $inventory_code_created);
	while ($k < $number) {

		$code = "O".$event_code.mt_rand(100000, 999999);
		$check = InventoryHelper::check_code($code);
		if (!$check) {
			$k++;
			$product[$k] = new stdClass();						
						$product[$k]->created_id = $max_created_id ;	
						$product[$k]->state = 1 ;			
						$product[$k]->code = $code;
						$product[$k]->barcode = $barcode;
						$product[$k]->value = $value;
						$product[$k]->merchent = $merchant;
						$product[$k]->type = 2;
						$product[$k]->price = $price;
						$product[$k]->expired=$expired;
						$product[$k]->event = $event;
					//	echo "<pre>";
						//	var_dump ($product[$k]);
					//	echo "</pre>";
			import_product($product[$k]);			
		}
	}
			
			header ("Location: index.php?option=com_inventory&view=vouchers");
	
	
}
if ($type == 3) { // XUẤT MÃ VOUCHER

								 
	$number = $jinput->get('number',0);
	$value = $jinput->get('value',0);
	$price = $jinput->get('price',0);
	$partner_id = $jinput->get('partner_id',0);
	$event = $jinput->get('event',"",string);
	$expired = $jinput->get('expired',"");
	$exported = InventoryHelper::export_codes($merchant, $value, $expired, $number);
	$number_code = count($exported);
	
	if ($number_code == $number) {
		$max_id = InventoryHelper::get_max_id('inventory_code_exported');
		
		$exported_code_table = new stdClass();
		$exported_code_table->id=$max_id;
		$exported_code_table->used_id = $userId;
		$exported_code_table->number = $number;
		$exported_code_table->value = $value;
		$exported_code_table->merchant =$merchant;
		$exported_code_table->price =$price;
		$exported_code_table->partner_id =$partner_id;
		$exported_code_table->event = $event;
		$exported_code_table->expired = $expired;
		$insert_code_exported = JFactory::getDbo()->insertObject('#__inventory_code_exported', $exported_code_table);
	
		?>
		<h2>Code vừa xuất</h2>
		
		<table class="table table-bordered" id="resultsTableExport">
			<thead>
				<tr>
					<th>Code</th>
					<th>BarCode</th>
					<th>Giá trị</th>
					<th>Hạn sử dụng</th>
					<th>NCC</th>
					<th>Đối tác</th>
					<th>Giá xuất</th>
				</tr>
			</thead>
			<tbody>
			
		<?php
		foreach ($exported as $code) {?>
			
			<tr>
				<td><?php echo $code->code?></td>
				<td><?php echo $code->barcode?></td>
				<td><?php echo $code->value?></td>
				<td><?php echo $expired?></td>
				<td><?php echo InventoryHelper::get_merchant_name($code->merchent)?></td>
				<td><?php echo InventoryHelper::get_merchant_name($partner_id)?></td>
				<td><?php echo $price?></td>
			</tr>
			<?php 
			$exported_code = new stdClass();
			$exported_code->exported_id = $max_id;
			$exported_code->barcode = $code->barcode;
			$exported_code->value = $code->value;
			$exported_code->price = $price;
			$exported_code->partner_id = $partner_id;
			$exported_code->code = $code->code;
			$exported_code->merchent = $code->merchent;
			$exported_code->type = $code->type;
			$exported_code->event = $event;
			$exported_code->expired = $expired;
			$exported_code->real_expired = $code->expired;
			$code->exported=1;
			$insert = JFactory::getDbo()->insertObject('#__inventory_voucher_exported', $exported_code);
			$update = JFactory::getDbo()->updateObject('#__inventory_voucher', $code, 'id');
			
			
		}
		?>
			</tbody>
		</table>
		<script>
			jQuery(document).ready(function() {
    
				
			 
					$("#resultsTableExport").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Code_Export_<?php echo date("d-m-Y")?>"
					});
				
			});
		</script>
		<?php 
		
	
	}else {
		echo "
			<script>
				alert('Không còn đủ CODE. Số code khả dụng: ".$number_code."');
			</script>
		";
	}	
}
if ($type == 4) { // UPLOAD FILE XUẤT CODE CHO KHÁCH
	$merchant = $jinput->get('merchant',0);
	$partner_id = $jinput->get('partner_id',0);
	$event = $jinput->get('event',"",string);
	$expired = $jinput->get('expired');
		$target_dir = JPATH_ROOT.'/images/import/';
		$target_file = $target_dir . date('m-d-Y_his').basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<p class='text-danger'>Sorry, file already exists.</p>";
			$uploadOk = 0;
		}


		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<p class='text-danger'>Sorry, your file was not uploaded.</p>";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			   // echo "<p class='text-success'>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
			   
			   if($imageFileType == "xlsx") {
				   $max_uploaded_id = InventoryHelper::get_max_id('inventory_code_uploaded');
				   doExport($target_file, $merchant, $sheet, $max_uploaded_id,$userId, $partner_id, $event, $expired);
				   
			   }else {
				   //doImportxls($target_file);
			   }
				
						
				/*if (!unlink($target_file))
					{
				  echo ("Error deleting $target_file");
				  }
				else
				  {
				  //echo ("<p class='text-info'>Deleted $target_file</p>");
				}*/
			} else {
				echo "<p class='text-danger'>Sorry, there was an error uploading your file.</p>";
			}
		}
}
// DO IMPORT
function doImport($fileExcel, $merchant, $sheet, $max_uploaded_id, $user_id,$price,$event,$value,$expired){
  		require_once (JPATH_COMPONENT.'/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		
		$rows = $xlsx->rows($sheet) ;
		$number=count($rows)-1;
		
  		$inventory_code_uploaded = new stdClass();
		$inventory_code_uploaded->id = $max_uploaded_id;
		$inventory_code_uploaded->file_name = $fileExcel;
		$inventory_code_uploaded->merchent = $merchant;
		$inventory_code_uploaded->sheet = $sheet;
		$inventory_code_uploaded->number = $number;
		$inventory_code_uploaded->user_id = $user_id; 
		$inventory_code_uploaded->price = $price; 
		$inventory_code_uploaded->event = $event; 
		$inventory_code_uploaded->value = $value; 
		$inventory_code_uploaded->expired = $expired;
		$inventory_code_uploaded->type = 1;		
		$inventory_code_uploaded = JFactory::getDbo()->insertObject('#__inventory_code_created', $inventory_code_uploaded);
  		
		$count_insert=0;
		
		foreach ($rows as $row) {
				if ($count_insert>0) {
						$product[$count_insert] = new stdClass();						
						$product[$count_insert]->created_id = $max_uploaded_id ;	
						$product[$count_insert]->state = 1 ;	
						$product[$count_insert]->price = $price ;									
						$product[$count_insert]->code = $row[0];
						$product[$count_insert]->barcode = $row[1];
						$product[$count_insert]->value = $value;
						$product[$count_insert]->merchent = $merchant;
						$product[$count_insert]->type = 1;
						$product[$count_insert]->expired = $expired;
						$product[$count_insert]->event = $event;
						//$product[$count_insert]->value = $row[2];
						import_product($product[$count_insert]);
						//echo strtotime($row[2])."<br/>";
				}
				$count_insert++;	
						
		}

		
			header ("Location: index.php?option=com_inventory&view=vouchers");
		


		
}
// DO EXPORT
function doExport($fileExcel, $merchant, $sheet, $max_uploaded_id, $user_id, $partner_id, $event, $expired){
  		
  		
		//$inventory_code_uploaded = JFactory::getDbo()->insertObject('#__inventory_code_uploaded', $inventory_code_uploaded);
  		require_once (JPATH_COMPONENT.'/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		$check_conditional = 0;
		$rows = $xlsx->rows($sheet) ;
		
		$remove_row = 0;
		foreach ($rows as $check_row) {
			if ($remove_row > 0) {
				$check_nr = $check_row[0];	
				$check_client = $check_row[1];
				$check_merchant_id = $check_row[3];
				$check_merchant_name = InventoryHelper::get_merchant_name($check_merchant_id);
				$check_value = $check_row[4];
				$check_quantity = $check_row[5];
				$check_exported = InventoryHelper::export_codes($check_merchant_id, $check_value, $expired, $check_quantity);
				$check_number_code = count($check_exported);
				if ($check_number_code < $check_quantity) {
					$unvailable[$check_nr] = new stdClass();
					$unvailable[$check_nr]->check_nr=$check_nr;
					$unvailable[$check_nr]->check_client=$check_client;
					$unvailable[$check_nr]->check_merchant_name=$check_merchant_name;
					$unvailable[$check_nr]->check_value=$check_value;
					$unvailable[$check_nr]->check_quantity=$check_quantity;
					$unvailable[$check_nr]->check_available_code=$check_number_code;
					$check_conditional = 1;
				}
			}
			$remove_row++;	
		}
		//echo "<pre>";
		//var_dump($unvailable);
		//echo "</pre>";
		if ($check_conditional == 1) {?> 
			<h2>Code không đủ</h2>
			<table class="table table-bordered" id="warningTableExportClient">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên Khách hàng</th>
					<th>NCC</th>
					<th>Giá trị</th>
					<th>Số lượng</th>
					<th>Code khả dụng</th>
				
				
					
				</tr>
			</thead>
			<tbody>
			<?php foreach ($unvailable as $unvailable_code) {?>
				<tr>
					<td><?php echo $unvailable_code->check_nr?></td>
					<td><?php echo $unvailable_code->check_client?></td>
					<td><?php echo $unvailable_code->check_merchant_name?></td>
					<td><?php echo $unvailable_code->check_value?></td>
					<td><?php echo $unvailable_code->check_quantity?></td>
					<td><?php echo $unvailable_code->check_available_code?></td>
				</tr>
			<?php }?>
			</tbody>
			</table>
			<script>
			jQuery(document).ready(function() {
    
				
			 
					$("#warningTableExportClient").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Code_Warning_<?php echo date("d-m-Y")?>"
					});
				
			});
		</script>
		<?php } else {
		$count_insert=0;
		?>
		<h2>Code đã xuất</h2>
		<table class="table table-bordered" id="resultsTableExportClient">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên Khách hàng</th>
					<th>SĐT</th>
					<th>NCC</th>
					<th>Giá trị</th>
					<th>Số lượng</th>
					<th>Code - BarCode</th>
				
					<th>Hạn sử dụng</th>
					<th>Đối tác</th>
					<th>Giá xuất</th>
				</tr>
			</thead>
			<tbody>
			
		<?php 
		foreach ($rows as $row) {
				if ($count_insert>0) {
						$exported_code = new stdClass();
						$nr = $row[0];	
						$client = $row[1];
						$phone = $row[2];
						$merchant_id = $row[3];
						$merchant_name = InventoryHelper::get_merchant_name($merchant_id);
						$value = $row[4];
						$quantity = $row[5];
						$price = $row[6];
						//$event="";
						//$expired = date('Y-m-d', strtotime("+35 days"));
					?>
						<tr>
							<td><?php echo $nr?></td>
							<td><?php echo $client?></td>
							<td><?php echo $phone?></td>
							<td><?php echo $merchant_name?></td>
							<td><?php echo $value?></td>
							<td><?php echo $quantity?></td>
							<td>
					<?php
						
						$exported = InventoryHelper::export_codes($merchant_id, $value, $expired, $quantity);
						if ($exported) {
						$max_id = InventoryHelper::get_max_id('inventory_code_exported');
						
						$exported_code_table = new stdClass();
						$exported_code_table->id=$max_id;
						$exported_code_table->used_id = $user_id;
						$exported_code_table->number = $quantity;
						$exported_code_table->value = $value;
						$exported_code_table->merchant =$merchant_id;
						$exported_code_table->partner_id =$partner_id;
						$exported_code_table->event = $event;
						$exported_code_table->price = $price;
						$exported_code_table->expired = $expired;
						$insert_code_exported = JFactory::getDbo()->insertObject('#__inventory_code_exported', $exported_code_table);
						$z=0;
						foreach ($exported as $code) {
							echo $code->code;
							if ($code->barcode)
								echo "-".$code->barcode;
							$z++;
							if ($z<$quantity) echo " | ";
							$exported_code = new stdClass();
							$exported_code->exported_id = $max_id;
							$exported_code->barcode = $code->barcode;
							$exported_code->value = $code->value;
							$exported_code->code = $code->code;
							$exported_code->merchent = $code->merchent;
							$exported_code->type = $code->type;
							$exported_code->event = $event;
							$exported_code->expired = $expired;
							$exported_code->price = $price;
							$exported_code->partner_id = $partner_id;
							$exported_code->real_expired = $code->expired;
							$code->exported=1;
							$insert = JFactory::getDbo()->insertObject('#__inventory_voucher_exported', $exported_code);
							$update = JFactory::getDbo()->updateObject('#__inventory_voucher', $code, 'id');
						}?>
							</td>
							
							<td><?php echo $expired?></td>
							<td><?php echo InventoryHelper::get_merchant_name($partner_id)?></td>
							<td><?php echo $price?></td>
						</tr>
						<?php 
					}else {
						echo "
							<script>
								alert('Không còn đủ CODE');
							</script>
						";
					}
						
				}
				$count_insert++;	
						
		}
		?>
			</tbody>
			</table>
			<script>
			jQuery(document).ready(function() {
    
				
			 
					$("#resultsTableExportClient").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Code_Export_<?php echo date("d-m-Y")?>"
					});
				
			});
		</script>
		<?php
	
		}
			//header ("Location: index.php?option=com_inventory&view=vouchers");
		


		
}
	function import_product($data) {
	// Create and populate an object.
	 
	// Insert the object into the user profile table.
	$result = JFactory::getDbo()->insertObject('#__inventory_voucher', $data);
	}
	function export_excel ($data){
		/*$data = array(
            array('name' => 'A', 'mail' => 'a@gmail.com', 'age' => 43),
            array('name' => 'C', 'mail' => 'c@gmail.com', 'age' => 24),
            array('name' => 'B', 'mail' => 'b@gmail.com', 'age' => 35),
            array('name' => 'G', 'mail' => 'f@gmail.com', 'age' => 22),
            array('name' => 'F', 'mail' => 'd@gmail.com', 'age' => 52),
            array('name' => 'D', 'mail' => 'g@gmail.com', 'age' => 32),
            array('name' => 'E', 'mail' => 'e@gmail.com', 'age' => 34),
            array('name' => 'K', 'mail' => 'j@gmail.com', 'age' => 18),
            array('name' => 'L', 'mail' => 'h@gmail.com', 'age' => 25),
            array('name' => 'H', 'mail' => 'i@gmail.com', 'age' => 28),
            array('name' => 'J', 'mail' => 'j@gmail.com', 'age' => 53),
            array('name' => 'I', 'mail' => 'l@gmail.com', 'age' => 26),
        );*/
		
		require_once (JPATH_COMPONENT.'/libs/PHPExcel.php');	
		// Create new PHPExcel object
				$objPHPExcel = new PHPExcel();
				// Set document properties
				$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
											 ->setLastModifiedBy("Maarten Balliauw")
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
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="code_exported.xlsx"');
					header('Cache-Control: max-age=0');
					// If you're serving to IE 9, then the following may be needed
					header('Cache-Control: max-age=1');
					// If you're serving to IE over SSL, then the following may be needed
					header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
					header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
					header ('Pragma: public'); // HTTP/1.0
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
					exit;
		
	}
	require_once (JPATH_COMPONENT.'/libs/PHPExcel.php');	
	// Here is the sample array of data
        $data = array(
            array('name' => 'A', 'mail' => 'a@gmail.com', 'age' => 43),
            array('name' => 'C', 'mail' => 'c@gmail.com', 'age' => 24),
            array('name' => 'B', 'mail' => 'b@gmail.com', 'age' => 35),
            array('name' => 'G', 'mail' => 'f@gmail.com', 'age' => 22),
            array('name' => 'F', 'mail' => 'd@gmail.com', 'age' => 52),
            array('name' => 'D', 'mail' => 'g@gmail.com', 'age' => 32),
            array('name' => 'E', 'mail' => 'e@gmail.com', 'age' => 34),
            array('name' => 'K', 'mail' => 'j@gmail.com', 'age' => 18),
            array('name' => 'L', 'mail' => 'h@gmail.com', 'age' => 25),
            array('name' => 'H', 'mail' => 'i@gmail.com', 'age' => 28),
            array('name' => 'J', 'mail' => 'j@gmail.com', 'age' => 53),
            array('name' => 'I', 'mail' => 'l@gmail.com', 'age' => 26),
        );
 
       

?>

<form action="<?php echo JRoute::_('index.php?option=com_inventory&view=vouchers'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="voucherList">
				<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
					<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value=""
							   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
					</th>
					<?php if (isset($this->items[0]->state)): ?>
						<th width="1%" class="nowrap center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
</th>
					<?php endif; ?>

									<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_INVENTORY_VOUCHERS_ID', 'a.`id`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_INVENTORY_VOUCHERS_CODE', 'a.`code`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
					BarCode
				</th>
				<th>QRCode</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_INVENTORY_VOUCHERS_VALUE', 'a.`value`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_INVENTORY_VOUCHERS_MERCHENT', 'a.`merchent`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_INVENTORY_VOUCHERS_EXPIRED', 'a.`expired`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_INVENTORY_VOUCHERS_EVENT', 'a.`event`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'Loại', 'a.`type`', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'Trạng thái', 'a.`used`', $listDirn, $listOrder); ?>
				</th>
					
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_inventory');
					$canEdit    = $user->authorise('core.edit', 'com_inventory');
					$canCheckin = $user->authorise('core.manage', 'com_inventory');
					$canChange  = $user->authorise('core.edit.state', 'com_inventory');
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if (isset($this->items[0]->state)): ?>
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'vouchers.', $canChange, 'cb'); ?>
</td>
						<?php endif; ?>

										<td>

					<?php echo $item->id; ?>
				</td>				<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'vouchers.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_inventory&task=voucher.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->code); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->code); ?>
				<?php endif; ?>

				</td>	
				<td>

					<?php echo $item->barcode; ?>
				</td>	
				<td>
					<?php  $qr_url = $item->merchent." | ".$item->code." | ".$item->value." | ".$item->expired?>
					<img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=<?php echo $qr_url?>&choe=UTF-8" />
				</td>		
				<td>

					<?php echo $item->value; ?>
				</td>				<td>

					<?php echo $item->merchent; ?>
				</td>				<td>

					<?php echo $item->expired; ?>
				</td>				<td>

					<?php echo $item->event; ?>
				</td>
				<td>

					<?php if ($item->type == 2) 
								echo "OneCard";
							else 
								echo "NCC";
					?>
				</td>
					<td>

					<?php if ($item->exported == 2)
							echo "Đã sử dụng";
						elseif ($item->exported == 1)
							echo "Đã xuất";	
						else 
							echo "Chưa sử dụng";?>
				</td>

					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>

<!-- UPLOAD VOUCHER TỪ FILE EXCEL -->
	<div class="modal hide fade" id="modal-test">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Upload Voucher từ file excel <a href="<?php echo JURI::root()?>mau-file-upload-voucher-code-cua-NCC.xlsx">File mẫu</a></h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_inventory&view=vouchers" method="post" enctype="multipart/form-data" id="importForm">
				<div class="row-fluid">
					<div class="span6">
						<p>Chọn file </p>
						<input type="file" name="fileToUpload" id="excel_file" size="40" class="inputbox" />
						<font color="red">(Max:&nbsp;<?php echo ini_get('upload_max_filesize'); ?>)</font>
						
						<p>Nhà cung cấp</p>
						<select name="merchant" required>
							<option value="">--Chọn nhà cung cấp--</option>
							<?php foreach ($merchants as $option) {?>
								<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
							<?php }?>
						</select>
						<p>Giá trị voucher</p>
							<input type="number" name="value" id="value" class="inputbox" />
						<p>Giá nhập</p>
						<input type="number" name="price" id="price" class="inputbox" required />	
							
					</div>
					<div class="span6">
						<p>Hạn sử dụng</p>		
							<input type="text" name="expired" value="<?php echo date('Y-m-d')?>" required />
						<p>Sự kiện</p>
							<input type="text" name="event" id="event" class="inputbox" />
						<p>Sheet</p>
						<input type="number" name="sheet" value="1"/>
					</div> 
				</div>
					
					
					<br/>
					<br/>
					<input type="submit" name="import" value="Nhập voucher" class="btn"/>	
					<input type="hidden" name="option" value="com_inventory" />
					<input type="hidden" name="type" value="1" />
					<input type="hidden" name="view" value="vouchers" />
					
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>
	
	
<!-- TẠO VOUCHER TỰ ĐỘNG -->	
	<div class="modal hide fade" id="modal-generate">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Tạo Voucher tự động</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_inventory&view=vouchers" method="post" >
				<div class="row-fluid">
					<div class="span6">
						<p>Nhà cung cấp</p>
							<select name="merchant" required>
								<option value="">--Chọn nhà cung cấp--</option>
								<?php foreach ($merchants as $option) {?>
									<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
								<?php }?>
							</select>
						<p>Giá trị voucher</p>
							<input type="number" name="value" id="value" class="inputbox" />	
						<p>Giá nhập</p>
						<input type="number" name="price" id="price" class="inputbox" required />	
					</div>
					<div class="span6">
						<p>Số lượng voucher</p>
						<input type="number" name="number" id="number" class="inputbox" />
						
						<p>Sự kiện</p>
						<input type="text" name="event" id="event" class="inputbox" />
						<p>Mã sự kiện (XX)</p>
						<input type="text" name="event_code" id="event_code" class="inputbox" />
						<p>Hạn sử dụng</p>
						<input type="text" name="expired" value="<?php echo date('Y-m-d')?>" required />
						
					</div>
				</div>
					
					
					<br/>
					<br/>
					<input type="submit" name="import" value="Tạo voucher" class="btn"/>	
					<input type="hidden" name="option" value="com_inventory" />
		
					<input type="hidden" name="type" value="2" />
					<input type="hidden" name="view" value="vouchers" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>

<!-- XUẤT VOUCHER -->	
<div class="modal hide fade" id="modal-export">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Xuất voucher</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_inventory&view=vouchers" method="post" >
				<div class="row-fluid">
					<div class="span6">
						<p>Nhà cung cấp</p>
						<select name="merchant" required>
							<option value="">--Chọn nhà cung cấp--</option>
							<?php foreach ($merchants as $option) {?>
								<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
							<?php }?>
						</select>
						<p>Số lượng voucher</p>
						<input type="number" name="number" id="number" class="inputbox" required />
						<p>Giá trị</p>
						<input type="number" name="value" id="value" class="inputbox" required />
						<p>Giá bán</p>
						<input type="number" name="price" id="price" class="inputbox" required />	
						</div>
					<div class="span6">
						<p>Đối tác</p>
						<select name="partner_id" required>
						<option value="">--Chọn đối tác--</option>
						<?php foreach ($partners as $option) {?>
							<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
						<?php }?>
						</select>
						<p>Sự kiện</p>
						<select name="event" id="event" required>
							<option value="">--Chọn sự kiện--</option>
							<?php foreach ($events as $option) {?>
								<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
							<?php }?>
						</select>
						<p>Hạn sử dụng</p>
						<input type="text" name="expired" value="<?php echo date('Y-m-d')?>" required />
					
					</div>
				</div>
					
					
					<br/>
					<br/>
					<input type="submit" name="import" value="Xuất voucher " class="btn" id="export_excel_file"/>	
					<input type="hidden" name="option" value="com_inventory" />
	
					<input type="hidden" name="type" value="3" />
					<input type="hidden" name="view" value="vouchers" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>
	

<!-- XUẤT VOUCHER CHO KHÁCH HÀNG-->	
<div class="modal hide fade" id="modal-export2">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Upload Voucher từ file excel <a href="<?php echo JURI::root()?>mau-file-xuat-code-cho-khach-hang.xlsx">File mẫu</a></h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_inventory&view=vouchers" method="post" enctype="multipart/form-data" id="importForm">
					<p>Chọn file </p>
					<input type="file" name="fileToUpload" id="excel_file" size="40" class="inputbox" />
					<font color="red">(Max:&nbsp;<?php echo ini_get('upload_max_filesize'); ?>)</font>
					<p>Đối tác</p>
						<select name="partner_id" required>
						<option value="">--Chọn đối tác--</option>
						<?php foreach ($partners as $option) {?>
							<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
						<?php }?>
						</select>
						<p>Sự kiện</p>
						<select name="event" id="event" required>
							<option value="">--Chọn sự kiện--</option>
							<?php foreach ($events as $option) {?>
								<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
							<?php }?>
						</select>
						<p>Hạn sử dụng</p>
						
						<input type="text" name="expired" value="<?php echo date('Y-m-d')?>" required />
					<p>Sheet</p>
					<input type="number" name="sheet" value="1"/>
					<br/>
					<br/>
					<input type="submit" name="import" value="Xuất voucher" class="btn"/>	
					<input type="hidden" name="option" value="com_inventory" />
					<input type="hidden" name="type" value="4" />
					<input type="hidden" name="view" value="vouchers" />
					
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>
	<script>
			jQuery(document).ready(function() {
    
				$('#export-btn').on('click', function(e){
					e.preventDefault();
					ResultsToTable();
				});
				
				function ResultsToTable(){    
					$("#resultsTable").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Code_Export_<?php echo date("d-m-Y")?>"
					});
				}
			});


		</script>
		<script type="text/javascript">
$(function() {
    $('input[name="expired"]').daterangepicker({
		locale: {
		  format: 'YYYY-MM-DD'
		},
        singleDatePicker: true,
        showDropdowns: true
    }, 
    function(start, end, label) {
        var years = moment().diff(start, 'years');
       // alert("You are " + years + " years old.");
    });
});
</script>