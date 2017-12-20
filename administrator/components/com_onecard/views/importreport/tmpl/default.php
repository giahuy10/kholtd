<?php
/// IMPORTING
/**
 * @version    CVS: 1.0.0
 * @package    Com_Uber
 * @author     Eddy Nguyen <contact@eddynguye.com>
 * @copyright  2017 Eddy Nguyen
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_onecard/assets/css/onecard.css');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$option = JRequest::getVar('option');
$view = JRequest::getVar('view');

$date_range = JRequest::getVar('daterange');
$date_range = explode(" - ",$date_range);
$date_from = JRequest::getVar('date_from');
$date_to = JRequest::getVar('date_to');
$onecard_voucher = JRequest::getVar('onecard_voucher');
$onecard_voucher=implode(",",$onecard_voucher);

$onecard_brand = JRequest::getVar('onecard_brand');
//var_dump($onecard_brand);
$onecard_brand=implode(",",$onecard_brand);
//var_dump($onecard_brand);
$type = JRequest::getVar('type');
$unit = JRequest::getVar('unit');
$onecard_partner = JRequest::getVar('onecard_partner');
$onecard_partner=implode(",",$onecard_partner);
$onecard_ncc = JRequest::getVar('onecard_ncc');
$onecard_ncc=implode(",",$onecard_ncc);
$onecard_event = JRequest::getVar('onecard_event');
$onecard_event=implode(",",$onecard_event);
$group_parter = JRequest::getVar('group_parter');
$group_event = JRequest::getVar('group_event');
$report_type = 1;
//echo $date_from."-".$date_to;
// Get a db connection.
$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);

// Select all articles for users who have a username which starts with 'a'.
// Order it by the created date.
// Note by putting 'a' as a second parameter will generate `#__content` AS `a`

	$query
    ->select(array('a.voucher as voucher_id','c.title as voucher_name','d.title as brand', 'count(*) as quantity', 'a.input_price as price', 'c.type', 'c.expired' ,'c.unit as unit','a.type as type_code','e.id as ncc_id','e.title as ncc' ))
    ->from($db->quoteName('#__onecard_code', 'a'))
  
	->join('INNER', $db->quoteName('#__onecard_voucher', 'c') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('c.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_brand', 'd') . ' ON (' . $db->quoteName('c.brand') . ' = ' . $db->quoteName('d.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_ncc', 'e') . ' ON (' . $db->quoteName('d.ncc') . ' = ' . $db->quoteName('e.id') . ')');
  
	if ($date_from)
		$query->where('DATE('.$db->quoteName('b.created') . ') >= '.$db->quote($date_from));
 	if ($date_to)
		$query->where('DATE('.$db->quoteName('b.created') . ') <= '.$db->quote($date_to));	
	
	if ($onecard_ncc)
	$query->where($db->quoteName('e.id') . ' IN ( '.$onecard_ncc.')');
	
		
	

	if ($onecard_voucher)
		$query->where($db->quoteName('c.id') . ' IN ( '.$onecard_voucher.')');
	if ($onecard_brand)
		$query->where($db->quoteName('d.id') . ' IN ( '.$onecard_brand.')');
	if ($type)
		$query->where($db->quoteName('c.type') . ' = '.$type);
	if ($unit)
		$query->where($db->quoteName('c.unit') . ' = '.$unit);
	
		$query->where($db->quoteName('a.state') . ' = 1');
$query->where($db->quoteName('a.virtual_code') . ' != 1');
	$group = array('a.voucher','a.input_price');
	
	$query->group($group);
	$query->order($db->quoteName('c.expired') . ' ASC');
//	echo $query->__toString();	
// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( ".datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
	  dateFormat: 'yy-mm-dd'
    }
	);
  } );
  </script>
<style>
	.chzn-container, .witdh100 {
		width: 100% !important;
	}
    label {
		display: block;
		margin-bottom: 5px;
		margin-top: 15px;
	}
	input, select {
		width: auto !important;
		margin-bottom: 0 !important;
	}
</style>
<style type="text/css" class="cp-pen-styles">

</style>
<script>

</script>
<h2>Báo cáo kho</h2>
<form method="post" action="index.php?option=com_onecard&view=importreport">
<div class="row-fluid">
	<div class="span2">
		<label>Merchant</label>
		<?php OnecardHelper::gen_select("onecard_ncc",$onecard_ncc)?>
	
		<label>Thương hiệu</label>
		<?php OnecardHelper::gen_select("onecard_brand",$onecard_brand)?>
	
		<label>Sản phẩm</label>
		<?php OnecardHelper::gen_select("onecard_voucher",$onecard_voucher)?>
		<label>Loại sản phẩm</label>
		<select name="type" onchange="this.form.submit()">
					<option value="">-- Lọc --</option>
					<option value="1" <?php if ($type == 1) echo "selected"?>>E-Code</option>
					<option value="2" <?php if ($type == 2) echo "selected"?>>Voucher</option>
					<option value="3" <?php if ($type == 3) echo "selected"?>>Product</option>
					<option value="4" <?php if ($type == 4) echo "selected"?>>Coupon</option>
				</select>
		<label>Phân phối</label>		
				<select name="unit" onchange="this.form.submit()">
					<option value="">-- Lọc --</option>
					<option value="1" <?php if ($unit == 1) echo "selected"?>>NCC</option>
					<option value="2" <?php if ($unit == 2) echo "selected"?>>OneCard</option>
					
				</select>		
	

		
		<label>Thời gian nhập kho</label>
		<p>Từ: <input type="text" class="datepicker" name="date_from" value="<?php echo $date_from ?>"></p>
		<p>Đến: <input type="text" class="datepicker" name="date_to" value="<?php echo $date_to ?>"></p>
		<br/><br/>
<button class="btn btn-info">Lọc dữ liệu</button>




	</div>

 
	<div class="span10" style="position:relative;">
	<div id="table-scroll" class="table-scroll">
	<div id="faux-table" class="faux-table" aria="hidden"></div>
	<div id="table-wrap" class="table-wrap">
	<table id="main-table" class="main-table">

	
<thead>	
	
	<tr>
		
		<th><span>ID</span></th>
		<th><span>Sản phẩm</span></th>
		<th><span>Nhãn hiệu</span></th>
		<th><span>Số lượng</span></th>
		<th><span><?php if ($report_type == 1) echo "Giá nhập"; else echo "Giá Xuất";?></span></th>
		
		<th><span>Tổng</th>
		
		
		<th><span><?php if ($report_type == 1) echo "Đã xuất"; else echo "Đối tác";?></span></th>
		<th><span><?php if ($report_type == 1) echo "Tồn kho"; else echo "Sự kiện";?></span></th>
		<th><span>Loại</span></th>
		<th><span>Phân phối</span></th>
		<th><span><?php if ($report_type == 1) echo "Hạn sử dụng"; else echo "Ngày Xuất";?></span></th>
		
	</tr>
</thead>
		<input type="hidden" name="option" value="<?php echo $option?>"/>
		<input type="hidden" name="view" value="<?php echo $view?>"/>
	<tbody>	
	<?php 
$excel_data = array();
$array_title = new stdClass();
$array_title->id = "ID";
$array_title->voucher = "Sản phẩm";
$array_title->brand = "Nhãn hiệu";
$array_title->quantity = "Số lượng";
$array_title->price = "Giá";
$array_title->total = "Thành tiền";
$array_title->exported = "Đã xuất";
$array_title->available = "Tồn kho";
$array_title->type = "Loại";
$array_title->ncc = "Phân phối";
$array_title->date = "Ngày hết hạn";
									//$array_title = array("Tên Voucher","Giá trị","Code","Barcode","Serial/PIN","Hạn sử dụng");
$excel_data[0] = $array_title;
$index=1;
		$total_import = 0;
		$total_import_value = 0;
		$total_inventory = 0;
		foreach ($results as $result) {
			$total_import += $result->quantity;
			$total_import_value += $result->quantity*$result->price;
			$total_inventory += OnecardHelper::get_number_of_voucher($result->voucher_id,1, $result->price)*$result->price;
			?>
		<tr>
			
			<td><?php echo $result->voucher_id?></td>
			<td><?php echo $result->voucher_name?></td>
			<td><?php echo $result->brand?></td>
			<td><?php echo $result->quantity?></td>
			<td><?php echo number_format($result->price)?></td>
			<td><?php echo number_format($result->quantity*$result->price)?></td>
			
			
			<td><?php if ($report_type == 1) echo OnecardHelper::get_number_of_voucher($result->voucher_id,"2,3",$result->price);  else echo $result->partner?></td>
			<td><?php if ($report_type == 1) echo OnecardHelper::get_number_of_voucher($result->voucher_id,1,$result->price);  else echo $result->event?></td>
			<td><?php echo OnecardHelper::get_type_name($result->type)?></td>
			
			<td><?php if ($result->unit == 2) echo "OneCard"; else echo "NCC"?></td>
			<td><?php if ($report_type == 1) echo date("d-m-Y",strtotime($result->expired)); else echo date("d-m-Y",strtotime($result->exported_date)); ?></td>
			
		</tr>
		<?php $row_export = new stdClass();
			$row_export->voucher_id = $result->voucher_id;
			$row_export->voucher_name = $result->voucher_name;
			$row_export->brand = $result->brand;
			$row_export->quantity = $result->quantity;
			$row_export->price = $result->price;
			$row_export->total = $result->quantity * $result->price;
			$row_export->exported = OnecardHelper::get_number_of_voucher($result->voucher_id, "2,3", $result->price);
			$row_export->available = OnecardHelper::get_number_of_voucher($result->voucher_id, 1, $result->price);
			$row_export->type = OnecardHelper::get_type_name($result->type);
			$row_export->ncc = ($result->unit == 2 ? "OneCard" : "NCC");
			$row_export->expired_date = date("d-m-Y", strtotime($result->expired));
			?>
				<?php $excel_data[$index] = $row_export;
			$index++;
			 }?>
	
		</tbody>
		<tfoot>
	<tr>
			<td>Tong</td>
			<td></td>
			<td></td>
			<td><?php echo number_format($total_import)?></td>
			
			<td></td>
			<td><?php echo number_format($total_import_value)?></td>
			<td></td>
			<td><?php echo number_format($total_inventory)?></td>
			<td></td>
			<td></td>
			<td></td>
		
		</tr>
		</tfoot>	
</table>
</div>
</div>
<?php $task = JRequest::getVar('task');
if ($task == "export") {

	OnecardHelper::export_excel($excel_data, date("H_i_d_m_Y"));
}

?>
	<a href="<?php echo JURI::root() ?>administrator/index.php?option=com_onecard&view=importreport&task=export"  class="btn btn-info"><span class="icon-download" aria-hidden="true"></span> Download</a>

</div>
<script>
	(function() {
   var mainTable = document.getElementById("main-table"); 
   var tableHeight = mainTable.offsetHeight; 
   if (tableHeight > 300) { 
  		var fauxTable = document.getElementById("faux-table");
		document.getElementById("table-wrap").className += ' ' + 'fixedON';
  		var clonedElement = mainTable.cloneNode(true); 
  		clonedElement.id = "";
  		fauxTable.appendChild(clonedElement);
   }
})();
	
			jQuery(document).ready(function() {
				
				$("#export").click(function(){
			 
					$("#main-table").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Code_Import_<?php echo date("d-m-Y")?>"
					});
				});	
				
			});
			
	
		</script>
</form>