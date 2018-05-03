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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_onecard/css/form.css');

$task = JRequest::getVar('task');
?>

<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {
	
    });

    Joomla.submitbutton = function (task) {
        if (task == 'export_voucher.cancel') {
            Joomla.submitform(task, document.getElementById('export_voucher-form'));
        }
        else {
            
            if (task != 'export_voucher.cancel' && document.formvalidator.isValid(document.id('export_voucher-form'))) {
                
                Joomla.submitform(task, document.getElementById('export_voucher-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>
<?php 
if ($task == "refund") {
	$db = JFactory::getDbo();

	$query = $db->getQuery(true);

	// Fields to update.
	$fields = array(
		$db->quoteName('status') . ' = 1',
		$db->quoteName('exported_id') . ' = 0',
		$db->quoteName('export_price') . ' = 0',
		$db->quoteName('exported_detail_id') . ' = 0'
	);

	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('exported_id') . ' = ' . $this->item->id,
		$db->quoteName('status') . ' = ' . 2
	);

	$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);

	$db->setQuery($query);

	$result = $db->execute();

	$object = new stdClass();
	$decode_store_data = json_decode($this->item->store_data);
	foreach ($decode_store_data as $key => $item) {
		$decode_store_data[$key]->code = "";
	}
	
	$object->id = $this->item->id;
	$object->store_data = json_encode($decode_store_data);
		//$object->store_data = 43;
	$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $object, 'id');
	// XOá chi tiết
	$db = JFactory::getDbo();

	$query = $db->getQuery(true);

// delete all custom keys for user 1001.
	$conditions = array(
		
		$db->quoteName('exported_id') . ' = ' . $this->item->id
	);

	$query->delete($db->quoteName('#__onecard_export_voucher_detail'));
	$query->where($conditions);

	$db->setQuery($query);

	$result = $db->execute();
}
?>
<?php 
	$exel_request = json_decode($this->item->store_data);
	// Lấy code cho file excel
	if ($task == "get_code") {
		$active_code = array();
		$not_enough = array();
		$post_code = array();
		foreach ($exel_request as $key=> $item) {
			$date = date("Y-m-d"); // current date
			$exchange_date = " +60 day";
			$date2 = strtotime(date("Y-m-d", strtotime($date)) . $exchange_date);
			$date2 = date("Y-m-d", $date2);
			$codes = OnecardHelper::export_codes_by_voucher($item->voucher, $date2, $item->quantity, $active_code);
			if ($codes && count($codes) >= $item->quantity) {
				$codes_text = array();
			$no_code =0;
				foreach ($codes as $code) {
					if ($code->serial)
						$codes_text[] = 'Voucher(' . $code->code . ')/PIN(' . $code->serial . ')';
					else
						$codes_text[] = $code->code;

					if ($code->type == 2){
						$no_code = 2;
					}
					if ($code->type == 3) {
						$no_code = 3;
					}	
					$active_code[] = $code->id;
					if ($code->eventoc_export) {
						$post_code[] = array(
							'coupon' => $code->code,

							'event_id' => $code->eventoc_export,
							'status' => 1,
							'created' => strtotime(date('Y-m-d 23:59:59')),
							'merchant_id' => $code->merchantoc,
							'end_time' => strtotime($date2 . " 16:59:59"),
							'price' => $code->value,
							'item_id' => $this->item->id,
							'cart_detail_id' => 82,
							'customer_id' => 1,
							'note' => 'Xuất TPBank'
						);	
					}
					

				}
				if ($no_code == 2) {
					$exel_request[$key]->code = "Voucher giấy";
				}else if ($no_code == 3) {
				$exel_request[$key]->code = "Sản phẩm";
				}
					else {
					$exel_request[$key]->code = implode(",", $codes_text);
				}
				
					
					
				
				
				$exel_request[$key]->expired = $date2;
			}else {
				$not_enough[] = array("voucher"=> $item->voucher."-".$item->gift, "expired"=>$date2, "quantity"=> $item->quantity, "number" => $item->number, "name" => $item->name, "available"=>count($codes));
			}
			

		}
	if ($not_enough) {
		echo "<h3>Code không đủ số lượng</h3>";
		echo "<table class='table table-bordered'>";
		echo "
			<tr>
				<th>STT</th>
				<th>Tên khách</th>
				<th>Voucher</th>
				<th>Hạn sử dụng</th>
				<th>Số lượng yêu cầu</th>
				<th>Số lượng còn trong kho</th>
			</tr>
		";
		foreach ($not_enough as $item) {
			echo "<tr>";
			echo "<td>" . $item['number'] . "</td><td>" . $item['name'] . "</td><td>".$item['voucher']. "</td><td> ". $item['expired']. "</td><td> ". $item['quantity']. "</td><td> " . $item['available'] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}	else {
		echo "<h3>Lấy code thành công</h3>";

		echo "<table class='table table-bordered'>";
		echo "
			<tr>
				<th>STT</th>
				<th>Tên khách</th>
				<th>Voucher</th>
				<th>Hạn sử dụng</th>
				<th>Số lượng yêu cầu</th>
				<th>Số lượng còn trong kho</th>
			</tr>
		";
		foreach ($exel_request as $item) {
			$item = (array)$item;
			echo "<tr>";
			echo "<td>" . $item['number'] . "</td><td>" . $item['name'] . "</td><td>" . $item['voucher'] . "</td><td> " . $item['expired'] . "</td><td> " . $item['quantity'] . "</td><td> " . $item['code'] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
			/*$object = new stdClass();
			
			$object->id = $this->item->id;	
			$object->store_data = json_encode($exel_request);
			$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $object, 'id');
		
			*/
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$fields = array(
				$db->quoteName('status') . ' = 2' ,
				$db->quoteName('exported_id') . ' = '.$this->item->id
			);
			$conditions = array(
				$db->quoteName('status') . ' = 1',
				$db->quoteName('id') . ' in (' . implode(",", $active_code). ')'
			);
			$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
			Onecardhelper::log_sql("post_code_".$this->item->id, json_encode($post_code));
			$result_post = OnecardHelper::postCurl('https://onecard.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
			
		Onecardhelper::log_sql("response_code_" . $this->item->id, $result_post);
			
		}
	}

?>
<form
    action="<?php echo JRoute::_('index.php?option=com_onecard&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="export_voucher-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ONECARD_TITLE_EXPORT_VOUCHER', true)); ?>
        
        <div class="row-fluid">
            <div class="span10 form-horizontal">
			<?php if ($this->item->id && !$this->item->is_excel) {?>
				<?php 
					$db = JFactory::getDbo();

					// Create a new query object.
					$query = $db->getQuery(true);

					// Select all articles for users who have a username which starts with 'a'.
					// Order it by the created date.
					// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
					$query
						->select(array('c.title','c.value','a.code','a.barcode','a.serial','f.expired' ))
						->from($db->quoteName('#__onecard_code', 'a'))
						->join('INNER', $db->quoteName('#__onecard_export_voucher', 'b') . ' ON (' . $db->quoteName('a.exported_id') . ' = ' . $db->quoteName('b.id') . ')')
						->join('INNER', $db->quoteName('#__onecard_voucher', 'c') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('c.id') . ')')
						
						->join('INNER', $db->quoteName('#__onecard_export_voucher_detail', 'f') . ' ON (' . $db->quoteName('a.exported_id') . ' = ' . $db->quoteName('f.exported_id') . ') and (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('f.voucher') . ')');
					
							$query->where($db->quoteName('a.exported_id') . ' = '.$this->item->id);
						
						$query->group($db->quoteName('a.code'));
						$query->order($db->quoteName('a.voucher') . ' DESC');
						//echo $query->__toString();	
					// Reset the query using our newly populated query object.
					$db->setQuery($query);

					// Load the results as a list of stdClass objects (see later for more options on retrieving data).

					$exported_codes = $db->loadObjectList();
					
					$excel_data = array();
					$array_title = new stdClass();
					$array_title->title="Tên Voucher";
					$array_title->value="Giá trị";
					$array_title->code="Code";
					$array_title->barcode="Barcode";
					$array_title->serial="Serial/PIN";
					$array_title->expired="Hạn sử dụng";
					//$array_title = array("Tên Voucher","Giá trị","Code","Barcode","Serial/PIN","Hạn sử dụng");
					$excel_data[0]=$array_title;
					$index = 1;?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Tên Voucher</th>
								<th>Giá trị</th>
								<th>Code</th>
								<th>Barcode</th>
								<th>Serial/PIN</th>
								<th>Hạn sử dụng</th>
							</tr>
						</thead>
				
					<?php foreach ($exported_codes as $code) {?>
						<tr>
							<td><?php echo $code->title?></td>
							<td><?php echo number_format($code->value)?></td>
							<td><?php echo $code->code?></td>
							<td><?php echo $code->barcode?></td>
							<td><?php echo $code->serial?></td>
							<td><?php echo $code->expired?></td>
						</tr>
						<?php $excel_data[$index]=$code;
						$index++;
					}
					//echo "<pre>";
					//var_dump($excel_data);
					?>
					</table>
					<?php 
					if ($task == "export") {
						
						OnecardHelper::export_excel($excel_data,$this->item->id."_".$this->item->event);
					}
						
				?>

				<a href="<?php echo JURI::root()?>administrator/index.php?option=com_onecard&view=export_voucher&layout=edit&id=<?php echo $this->item->id?>&task=export"  class="btn btn-info"><span class="icon-download" aria-hidden="true"></span> Download Codes</a>
				
					
			<?php } else {?>

				<!-- CODE DANH CHO KHACH HANG TPBANK -->
				<table class="table table-bordered">
						<thead>
							<tr>
								<th>STT</th>
								<th>Tên khách hàng</th>
								<th>Số CIF tại TPB</th>
								<th>Loại quà</th>
								<th>Số lượng quà đăng ký</th>
								
								<th>Mã đối tác</th>
								<th>Địa chỉ nhận quà</th>
								<th>SĐT khách hàng</th>
								<th>Mã voucher</th>
								<th>Giá</th>
								
								
							

							</tr>
						</thead>
					<?php 
				
				
				
				//echo "<pre>";
				//var_dump($exel_request);
				//echo "</pre>";
				//echo $this->item->store_data;
				$excel_data_tp = array();
				$array_title = new stdClass();
				$array_title->number = "STT";
				$array_title->name = "Tên khách hàng";
				$array_title->cif = "Số CIF tại TPB";
				$array_title->gift = "Loại quà";
				$array_title->quantity = "Số lượng quà đăng ký";
				$array_title->date_register = "Ngày đăng ký";
				$array_title->code = "Mã đối tác";
				$array_title->address = "Địa chỉ nhận quà";
				$array_title->phone = "SĐT khách hàng";
				$array_title->note = "Ghi chú";
				//$array_title->note_trangctt = "Note Trangctt";
				//$array_title->expired = "Hạn sử dụng";
				
					//$array_title = array("Tên Voucher","Giá trị","Code","Barcode","Serial/PIN","Hạn sử dụng");
				$excel_data_tp[0] = $array_title;
				$index = 1;
					?>
					<?php foreach ($exel_request as $code) { ?>
						<tr>
							<td><?php echo $code->number ?></td>
							<td><?php echo $code->name ?></td>
							<td><?php echo $code->cif ?></td>
							<td><?php echo $code->gift ?></td>
							<td><?php echo $code->quantity ?></td>
							
							<td><?php echo $code->code ?></td>
							<td><?php echo $code->address ?></td>
							<td><?php echo $code->phone ?></td>
							<td><?php echo $code->voucher ?></td>
							<td><?php echo $code->price ?></td>
						
						</tr>
						
				
					<?php 
						$array_value = new stdClass();
						$array_value->number = $code->number;
						$array_value->name = $code->name;
						$array_value->cif = $code->cif;
						$array_value->gift = $code->gift;
						$array_value->quantity = $code->quantity;
						$array_value->date_register = $code->date_register;
						$array_value->code = $code->code;
						$array_value->address = $code->address;
						$array_value->phone = $code->phone;
						$array_value->note = $code->note;
						//$array_value->note_trangctt = $code->note_trangctt;
						//$array_value->expired = $code->expired;
						$excel_data_tp[$index] = $array_value;
						$index++;
					?>
				
				
				<?php }?>
					</table>
					<?php 
						if ($task == "export_tp") {

							OnecardHelper::export_excel($excel_data_tp, $this->item->id . "_" . $this->item->event);
						}
					?>
					<a href="<?php echo JURI::root() ?>administrator/index.php?option=com_onecard&view=export_voucher&layout=edit&id=<?php echo $this->item->id ?>&task=export_tp"  class="btn btn-info"><span class="icon-download" aria-hidden="true"></span> Download excel</a>
					<a href="<?php echo JURI::root() ?>administrator/index.php?option=com_onecard&view=export_voucher&layout=edit&id=<?php echo $this->item->id ?>&task=get_code"  class="btn btn-info"><span class="icon-check-circle" aria-hidden="true"></span> Lấy codes</a>
				<?php }?>
                <fieldset class="adminform">
				
				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
<?php echo $this->form->renderField('ordering'); ?>

<?php echo $this->form->renderField('event'); ?>
<?php echo $this->form->renderField('note'); ?>
<?php 
if ($this->item->id)
echo $this->form->renderField('list_templates'); ?>

<?php echo $this->form->renderField('created'); ?>
<?php echo $this->form->renderField('checked_out'); ?>
<?php echo $this->form->renderField('checked_out_time'); ?>
<?php echo $this->form->renderField('created_by'); ?>
<?php echo $this->form->renderField('modified_by'); ?>


                   
                </fieldset>
            </div>
        </div>
        
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
<!-- Xoá code -->	
<div class="modal hide fade" id="modal-delete">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Hoàn code đã xuất</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			<h3>
			 <a href="<?php echo JURI::root() ?>administrator/index.php?option=com_onecard&view=export_voucher&layout=edit&id=<?php echo $this->item->id ?>&task=refund"  class="btn btn-info"><span class="icon-remove" aria-hidden="true"></span> Hoàn lại các code đã xuất</a>
			 </h3>
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
		<h3>Upload Voucher từ file excel <a href="<?php echo JURI::root()?>mau-file-xuat-code-cho-khach-hang3.xlsx">File mẫu</a></h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_onecard&view=export_voucher&layout=edit&id=<?php echo $this->item->id?>" method="post" enctype="multipart/form-data" id="importForm">
					<p>Chọn file </p>
					<input type="file" name="fileToUpload" id="excel_file" size="40" class="inputbox" />
					<font color="red">(Max:&nbsp;<?php echo ini_get('upload_max_filesize'); ?>)</font>
					
						
					
					
					<br/>
					<br/>
					<input type="submit" name="export" value="Xuất voucher" class="btn"/>	
					<input type="hidden" name="option" value="com_onecard" />
					<input type="hidden" name="layout" value="edit" />
					<input type="hidden" name="id" value="<?php echo $this->item->id?>" />
					<input type="hidden" name="voucher_id" value="<?php echo $this->item->id?>" />
					<input type="hidden" name="view" value="export_voucher" />
					
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
	<?php 
	$jinput = JFactory::getApplication()->input;
	$export = $jinput->get('export');
	if ($export) {
	
	$voucher_id = $jinput->get('voucher_id');
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

				   doExport($target_file,  $this->item->id);
				   echo "ok";
			   }else {
				   //doImportxls($target_file);
				   echo "Khong ho tro dinh dang xls";
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
	function doExport ($fileExcel, $export_id){
		require_once (JPATH_COMPONENT . '/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);
		$rows = $xlsx->rows(1);
			$object = new stdClass();
			
			/*$object->id = $export_id;	
			$object->store_data = json_encode($rows);
			$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $object, 'id');*/
	 
		$remove_row = 0;
		$store_data = array();
		$values = array();
		$values_detail = array();
		$data_json_array = array();
	$date = date("Y-m-d"); // current date
	$exchange_date = " +" . $voucher->expired . " day";
	$date2 = strtotime(date("Y-m-d", strtotime($date)) . $exchange_date);
	$date2 = date("Y-m-d", $date2);
		foreach ($rows as $inser_row) {

			if ($inser_row[0] >=1) {
				
				$profile = new stdClass();
				$profile->export_id = $export_id;
				$profile->number = $inser_row[0];
				$profile->name = $inser_row[1];
				$profile->cif = $inser_row[2];
				$profile->gift = $inser_row[3];
				$profile->quantity = $inser_row[4];
			//	$profile->date_register = date("Y-m-d",strtotime($inser_row[5]));
			$profile->date_register = $inser_row[5];
				$profile->code = $inser_row[6];
				$profile->address = $inser_row[7];
				$profile->phone = $inser_row[8];
				$profile->note = $inser_row[9];
				//$profile->note_trangctt = $inser_row[10];
				$profile->voucher = $inser_row[10];
				$profile->price = $inser_row[11];
				$store_data[] = $profile;
				//$result = JFactory::getDbo()->insertObject('#__onecard_excel', $profile);
				$json_id = $remove_row - 5;
				$data_json_array[]= '"list_templates' . $json_id . '":{"voucher":"' . $inser_row[11] . '","number":"' . $inser_row[4] . '","price":"' . $inser_row[12] . '","expired":"35"}';
				$values[] = "('". $profile->export_id . "','" . $profile->number . "','" . $profile->name . "','" . $profile->cif . "','" . $profile->gift . "','" . $profile->quantity . "','" . $profile->date_register . "','" . $profile->code . "','" . $profile->address . "','" . $profile->phone . "','" . $profile->note . "','" . $profile->voucher . "','" . $profile->price . "')";
				$values_detail[]= "('" . $profile->voucher . "','" . $profile->quantity . "','" . $profile->price . "','" . $profile->export_id . "','" . $date2 . "')";
			}
			$remove_row ++;
		
			
		}
		$values_insert = implode(",",$values);
	$values_detail_insert = implode(",", $values_detail);
		$db = JFactory::getDbo();
		$query = "
			INSERT INTO #__onecard_excel (export_id, number,name,cif,gift,quantity,date_register,code,address,phone,note,voucher,price)
			VALUES 
			
			". $values_insert;
			$db->setQuery($query);
			$db->query();
	$query2 = "
			INSERT INTO #__onecard_export_voucher_detail (voucher, number,price,exported_id, expired)
			VALUES 
			
			" . $values_detail_insert;
	$db->setQuery($query2);
	$db->query();	
		//var_dump($data_json_array);
		$data_json = implode(",", $data_json_array);
		$object = new stdClass();
		$object->is_excel = 1;
		$object->id = $export_id;
		//$object->list_templates = '{' . $data_json . '}';
		$object->store_data = json_encode($store_data);
		//$object->store_data = 43;
		$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $object, 'id');
		header("Location: index.php?option=com_onecard&view=export_voucher&layout=edit&id=". $export_id);
	}	
	function doExport_old($fileExcel, $voucher_id, $expired, $export_id){
  		
  		
		//$inventory_code_uploaded = JFactory::getDbo()->insertObject('#__inventory_code_uploaded', $inventory_code_uploaded);
  		require_once (JPATH_COMPONENT.'/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		$check_conditional = 0;
		$rows = $xlsx->rows() ;
		
		$remove_row = 0;
		foreach ($rows as $check_row) {
			if ($remove_row > 0 && $check_row[0] != "") {
				$check_nr = $check_row[0];	
				$check_client = $check_row[1];
				$check_merchant_id = $check_row[3];
				$export_type = $check_row[7];
				$export_expired = date("Y-m-d", strtotime($row[8]));
				$check_merchant_name = OnecardHelper::get_merchant_name($check_merchant_id);
				$check_value = $check_row[4];
				$check_quantity = $check_row[5];
				$check_exported = OnecardHelper::export_codes($check_merchant_id, $check_value, $export_expired, $check_quantity);
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
			
		<?php } else {
		$count_insert=0;
		?>
		<h2>Code đã xuất</h2>
		<a href="#" id="export_code_btn" class="btn btn-info"><span class="icon-download" aria-hidden="true"></span> Download Codes</a>
		<table class="table table-bordered" id="export_code_z">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên Khách hàng</th>
					<th>SĐT</th>
					<th>Nhãn hiệu</th>
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
		$export_tpb = array();
		$array_title_tpb = new stdClass();
		$array_title_tpb->number="STT";
		$array_title_tpb->customer="Tên Khách hàng";
		$array_title_tpb->phone="SĐT";
		$array_title_tpb->brand="Nhãn hiệu";
		$array_title_tpb->value="Giá trị";
		$array_title_tpb->quantity="Số lượng";
		$array_title_tpb->code="Code - BarCode";
		$array_title_tpb->expired="Hạn sử dụng";
		$array_title_tpb->partner="Đối tác";
		$array_title_tpb->price="Giá xuất";
		//$array_title = array("Tên Voucher","Giá trị","Code","Barcode","Serial/PIN","Hạn sử dụng");
		$export_tpb[0]=$array_title_tpb;
		$data_json = "";
		$post_code = array();	
		foreach ($rows as $row) {
				if ($count_insert>0 && $row[1]!="") {
						$exported_code = new stdClass();
						$nr = $row[0];	
						$client = $row[1];
						$phone = $row[2];
						$merchant_id = $row[3];
						$merchant_name = OnecardHelper::get_merchant_name($merchant_id);
						$value = $row[4];
						$quantity = $row[5];
						$price = $row[6];
						$type = $row[7];
						$expired_excel = date("Y-m-d",strtotime($row[8]));
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
						
						$exported = OnecardHelper::export_codes($merchant_id, $value, $expired_excel, $quantity);
						if ($exported) {
					
						$user = JFactory::getUser();
						$exported_code_table = new stdClass();
						$exported_code_table->voucher=OnecardHelper::get_voucher_id($merchant_id,$value);
						$exported_code_table->created_by = $user->id;
						$exported_code_table->number = $quantity;
						$exported_code_table->price = $price;
						$exported_code_table->expired = $expired_excel;
						
						//
					$now = date("Y-m-d"); // or your date as well
					$startTimeStamp = strtotime($now);
					$endTimeStamp = strtotime($expired_excel);

					$timeDiff = abs($endTimeStamp - $startTimeStamp);

					$numberDays = $timeDiff / 86400;  // 86400 seconds in one day

					$expired_number = intval($numberDays);	

						$exported_code_table->exported_id = $voucher_id;
						
						$insert_code_exported = JFactory::getDbo()->insertObject('#__onecard_export_voucher_detail', $exported_code_table);
						$json_id = $count_insert-1;

						
						$data_json .= '"list_templates'.$json_id.'":{"voucher":"'.$exported_code_table->voucher.'","number":"'.$quantity.'","price":"'.$price.'","expired":"'. $expired_number .'"}';
						$real_record = count($rows) - 1;
						if ($count_insert < $real_record){
							$data_json .=',';
						}
						$z=0;
						if ($type == 1)
							$code_value = "";
						else 	
							$code_value = "'";
						
						foreach ($exported as $code) {
							if ($type == 1)
								$code_value .= 'Voucher('.$code->code.')/PIN('.$code->serial.')';
							else
								$code_value .= $code->code;
							$z++;
							if ($z<$quantity) {
								$code_value .= ",";
							};
							echo $code_value;
							$code->status=2;
							$code->exported_id=$voucher_id;
							$code->export_price = $price;
							$update = JFactory::getDbo()->updateObject('#__onecard_code', $code, 'id');


							// POST CODE TO ONECARD
							$post_code[] = array(
								'coupon' => $code->code,
								'event_id' => OnecardHelper::get_event_oc_id($exported_code_table->voucher),
								'status' => 1,
								'created' => strtotime(date('Y-m-d 23:59:59')),
								'merchant_id' => OnecardHelper::get_merchant_oc_id($merchant_id),
								'end_time' => strtotime($expired_excel . " 16:59:59"),
								'price' => $value,
								'item_id' => $export_id,
								'cart_detail_id' => 82,
								'customer_id' => 1
							);
						//$result_post = OnecardHelper::postCurl('https://onecard.ycar.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
						//Onecardhelper::log_sql("post_url" . $item, $data_json);

						}?>
							</td>
							
							<td><?php echo $expired?></td>
							<td><?php echo OnecardHelper::get_merchant_name($merchant_id)?></td>
							<td><?php echo $price?></td>
						</tr>
						<?php 
							$array_row_tpb = new stdClass();
							$array_row_tpb->number=$nr;
							$array_row_tpb->customer=$client;
							$array_row_tpb->phone=$phone;
							$array_row_tpb->brand=$merchant_name;
							$array_row_tpb->value=$value;
							$array_row_tpb->quantity=$quantity;
							$array_row_tpb->code=$code_value;
							$array_row_tpb->expired=$expired_excel;
							$array_row_tpb->partner=OnecardHelper::get_merchant_name($merchant_id);
							$array_row_tpb->price=$price;
							//$array_title = array("Tên Voucher","Giá trị","Code","Barcode","Serial/PIN","Hạn sử dụng");
							$export_tpb[]=$array_row_tpb;


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
		$result_post = OnecardHelper::postCurl('https://onecard.vn/api.php?act=cart&code=export_code_from_stock', json_encode($post_code));
		Onecardhelper::log_sql("post_url", $result_post);
		$object = new stdClass();

		// Must be a valid primary key value.
		$object->id = $voucher_id;
		$object->excel_data = json_encode($export_tpb);
		$object->list_templates = '{' . $data_json . '}';
		$object->is_exported_code = 1;
		//$object->note = "Xuất code cho khách từ file excel";
		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $object, 'id');
		OnecardHelper::export_excel($export_tpb,"CODE_EXPORTED".time());
		
		?>
			</tbody>
			</table>
			
		<?php
	
		}
			//header ("Location: index.php?option=com_inventory&view=vouchers");
		


		
}	
	
	?>
	