<?php
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
$date_from = $date_range[0];
$date_to = $date_range[1];
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

$onecard_event = JRequest::getVar('onecard_event');
$onecard_event=implode(",",$onecard_event);
$group_parter = JRequest::getVar('group_parter');
$group_event = JRequest::getVar('group_event');
$group_voucher = JRequest::getVar('group_voucher');


if (JRequest::getVar('report_base')) {
	$report_base = JRequest::getVar('report_base');
}else {
	$report_base = "d.id";
}
$report_type = 2;
//echo $date_from."-".$date_to;
// Get a db connection.
$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);

// Select all articles for users who have a username which starts with 'a'.
// Order it by the created date.
// Note by putting 'a' as a second parameter will generate `#__content` AS `a`

	$query
    ->select(array('a.voucher as voucher_id','c.title as voucher_name','d.title as brand', 'a.number as quantity', 'a.price as price', 'b.event', 'c.type', 'a.expired', 'f.title as partner', 'e.title as event','c.unit as unit' ,'b.created as exported_date','f.id as partner_id' ))
    ->from($db->quoteName('#__onecard_export_voucher_detail', 'a'))
    ->join('INNER', $db->quoteName('#__onecard_export_voucher', 'b') . ' ON (' . $db->quoteName('a.exported_id') . ' = ' . $db->quoteName('b.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_voucher', 'c') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('c.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_brand', 'd') . ' ON (' . $db->quoteName('c.brand') . ' = ' . $db->quoteName('d.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_event', 'e') . ' ON (' . $db->quoteName('b.event') . ' = ' . $db->quoteName('e.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_partner', 'f') . ' ON (' . $db->quoteName('e.partner') . ' = ' . $db->quoteName('f.id') . ')');
	if ($date_from)
	$query->where('DATE('.$db->quoteName('b.created') . ') >= '.$db->quote($date_from));
 	if ($date_to)
	$query->where('DATE('.$db->quoteName('b.created') . ') <= '.$db->quote($date_to));	
	if ($onecard_partner)
	$query->where($db->quoteName('f.id') . ' IN ( '.$onecard_partner.')');
	if ($onecard_event)
	$query->where($db->quoteName('e.id') . ' IN ( '.$onecard_event.')');
		

	if ($onecard_voucher)
		$query->where($db->quoteName('c.id') . ' IN ( '.$onecard_voucher.')');
	if ($onecard_brand)
		$query->where($db->quoteName('d.id') . ' IN ( '.$onecard_brand.')');
	if ($type)
		$query->where($db->quoteName('c.type') . ' = '.$type);
	if ($unit)
		$query->where($db->quoteName('c.unit') . ' = '.$unit);
	
	//$query->where($db->quoteName('a.state') . ' = 1');
	$query->where($db->quoteName('b.state') . ' = 1');
	$query->where($db->quoteName('c.state') . ' = 1');
	$query->where($db->quoteName('d.state') . ' = 1');
	$query->where($db->quoteName('e.state') . ' = 1');
	$query->where($db->quoteName('f.state') . ' = 1');
    
	
	//$query->group($report_base);
	$query->order($db->quoteName('a.exported_id') . ' DESC');
//	echo $query->__toString();	
// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
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
<h2>Báo cáo xuat kho</h2>
<form method="post" action="index.php?option=com_onecard&view=exportreport">
<div class="row-fluid">
	<div class="span2">
		
	
		<label>Thương hiệu</label>
		<?php OnecardHelper::gen_select("onecard_brand",$onecard_brand)?>
	
		<label>Sản phẩm </label>
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
	

		<label>Đối tác </label>
		<?php OnecardHelper::gen_select("onecard_partner",$onecard_partner)?>
	
		<label>Sự kiện </label>
		<?php OnecardHelper::gen_select("onecard_event",$onecard_event)?>
		<label>Thời gian</label>
		<?php 
		$date = date("Y-m-d");// current date
	$date2 = strtotime(date("Y-m-d", strtotime($date)) . " -1 month");

?>
		<input type="text" name="daterange" value="<?php echo date("Y-m-d",$date2);?> - <?php echo date("Y-m-d")?>" class="witdh100" />
		<br/><br/>
<button class="btn btn-info">Lọc dữ liệu</button>

<script type="text/javascript">
$(function() {
    $('input[name="daterange"]').daterangepicker({
		locale: {
            format: 'YYYY-MM-DD'
        }
	});
});
</script>

	</div>

 
	<div class="span10">
	<div id="table-scroll" class="table-scroll">
	<div id="faux-table" class="faux-table" aria="hidden"></div>
	<div id="table-wrap" class="table-wrap">
	<table id="main-table" class="main-table">
	
		
	<thead>
	<tr>
		
		<th>ID</th>
		<th>Sản phẩm</th>
		<th>Nhãn hiệu</th>
		<th>Số lượng</th>
		<th><?php if ($report_type == 1) echo "Giá nhập"; else echo "Giá Xuất";?></th>
		
		<th>Tổng</th>
		<th>Loại</th>
		<th>Phân phối</th>
		
		<th><?php if ($report_type == 1) echo "Đã xuất"; else echo "Đối tác";?></th>
		<th><?php if ($report_type == 1) echo "Tồn kho"; else echo "Sự kiện";?></th>
		<th><?php if ($report_type == 1) echo "Hạn sử dụng"; else echo "Ngày Xuất";?></th>
		
	</tr>
	</thead>
		<input type="hidden" name="option" value="<?php echo $option?>"/>
		<input type="hidden" name="view" value="<?php echo $view?>"/>
		<tbody>
	<?php 
		$total_quantity = 0;
		$total_revenue = 0;
		foreach ($results as $result) {
			$total_quantity+=$result->quantity;
			if ($result->price == 0) {
				$result->price = OnecardHelper::get_voucher_price($result->voucher_id,$result->partner_id);
			}elseif($result->price == 1) {
				$result->price = 0;
			}
				

			$total_revenue+=$result->quantity*$result->price;
			?>
		<tr>
			
			<td><?php echo $result->voucher_id?></td>
			<td><?php echo $result->voucher_name?></td>
			<td><?php echo $result->brand?></td>
			<td><?php echo $result->quantity?></td>
			<td><?php echo number_format($result->price)?></td>
			
			<td><?php echo number_format($result->quantity*$result->price)?></td>
			<td><?php echo OnecardHelper::get_type_name($result->type)?></td>
			<td><?php if ($result->unit == 2) echo "OneCard"; else echo "NCC"?></td>
			
			<td><?php if ($report_type == 1) echo OnecardHelper::get_number_of_voucher($result->voucher_id,2);  else echo $result->partner?></td>
			<td><?php if ($report_type == 1) echo OnecardHelper::get_number_of_voucher($result->voucher_id,1);  else echo $result->event?></td>
			<td><?php if ($report_type == 1) echo date("d-m-Y",strtotime($result->expired)); else echo date("d-m-Y",strtotime($result->exported_date)); ?></td>
			
		</tr>
	<?php }?>
		</tbody>
	<tfoot>
		<tr>
			<td>Tong</td>
			<td></td>
			<td></td>
			<td><?php echo number_format($total_quantity)?></td>
			<td></td>
			<td><?php echo number_format($total_revenue)?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		
			<td></td>
		</tr>
		</tfoot>
</table>
		</div></div>
<a href="#" class="btn btn-success" onclick="export()" id="export"><span class="icon-download" aria-hidden="true"></span>Download Excel file</a>
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
						filename: "Code_Export_<?php echo date("d-m-Y")?>"
					});
				});	
				
			});
			
	
		</script>
</form>