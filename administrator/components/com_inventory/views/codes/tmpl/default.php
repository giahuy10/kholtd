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
$document->addScript(JUri::root() . 'administrator/components/com_inventory/assets/js/jquery.table2excel.js');


$user      = JFactory::getUser();
$userId    = $user->get('id');
$jinput = JFactory::getApplication()->input;
$merchant = $jinput->get('merchant',0);
$renew = $jinput->get('renew',0);
if ($renew) {
	$renew = $jinput->get('renew',0);
	$merchant_id = $jinput->get('merchant_id',0);
	$value = $jinput->get('value',0);
	$expired = $jinput->get('expired');
	$code = InventoryHelper::get_code_to_renew($merchant_id,$value,$expired);
	//var_dump($code);
	$submit = $jinput->get('submit');
	if ($submit) {
		$renew_date = $jinput->get('renew_date');
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
			$db->quoteName('expired') . ' = ' . $db->quote($renew_date)
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('merchent') . ' = '.$merchant_id, 
			$db->quoteName('expired') . ' = ' . $db->quote($expired),
			$db->quoteName('value') . ' = ' . $value
		);

		$query->update($db->quoteName('#__inventory_voucher'))->set($fields)->where($conditions);
		//echo $query->__toString();
		$db->setQuery($query);

		$result = $db->execute();
		echo "Voucher đã được gia hạn <br/>";
		echo '<a href="index.php?option=com_inventory&view=codes" class="btn btn-info">Quay lại</a>';
	} else {
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
	<form action="" method="post">
		<table>
			<tr>
				<td>Nhà cung cấp</td>
				<td><?php echo InventoryHelper::get_merchant_name($merchant_id)?></td>
			</tr>
			<tr>
				<td>Giá trị voucher</td>
				<td><?php echo $value?></td>
			</tr>
			<tr>
				<td>Hạn sử dụng (hiện tại)</td>
				<td><?php echo $expired?></td>
			</tr>
			<tr>
				<td>Gia hạn</td>
				<td><input type="text" name="renew_date" value="<?php echo $expired?>" required /></td>
			</tr>
			<tr>
				
				<td colspan="2"><input type="submit" name="submit" value="Gia hạn" class="btn btn-info"/></td>
			</tr>
		</table>
		
	</form>
	<script type="text/javascript">
$(function() {
    $('input[name="renew_date"]').daterangepicker({
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
	<?php }?>
<?php }else {
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>
<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<form class="form-inline" action="index.php?option=com_inventory&view=codes" method="post">
		  <select name="merchant" class="input-large" onchange="this.form.submit()">
				<?php $merchants = InventoryHelper::get_merchants('com_inventory'); ?>
				<option value="">--Chọn nhà cung cấp--</option>
						<?php foreach ($merchants as $option) {?>
							<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
						<?php }?>
		  </select>
		  <a class="btn btn-default" id="export-btn">Xuất file Excel</a>
		 <input type="hidden" name="option" value="com_inventory">
		 <input type="hidden" name="view" value="codes">
		</form>
		 
		<table class="table table-bordered text-center" id="resultsTable" data-tableName="Test Table 2">
			<thead>
				<tr>
					<th>Nhà cung cấp</th>
					<th>Giá trị</th>
					<th>Giá nhập</th>
					<th>Sự kiện</th>
					<th>Ngày hết hạn</th>
					<th>Loại</th>
					<th>Tổng code</th>
					<th>Đã xuất</th>
					<th>Đã sử dụng</th>
					<th>Tồn kho</th>
				</tr>
			</thead>
		<?php 
			$results = InventoryHelper::get_vouchers($merchant);
			 $total_total=0;
			 $total_exported=0;
			 $total_used=0;
			 $total_available=0;
			 if ($results) {
			foreach ($results as $voucher) {?>
				<?php 
					$total = InventoryHelper::get_number_code_of_voucher($voucher->id);
					$total_total+=$total;
					$exported = InventoryHelper::get_number_code_of_voucher($voucher->id,1);
					$total_exported+=$exported;
					$used = InventoryHelper::get_number_code_of_voucher($voucher->id,2);
					$total_used+=$used;
					$available = $total-$exported;
					$total_available+=$available;
				?>
				<tr>
					<td><?php echo $voucher->merchent." ".InventoryHelper::get_merchant_name($voucher->merchent)?></td>
					<td><?php echo number_format($voucher->value)?></td>
					<td><?php echo number_format($voucher->price)?></td>
					<td><?php echo $voucher->event?></td>
					<td><?php echo $voucher->expired?>
						 <a href="index.php?option=com_inventory&view=codes&renew=1&merchant_id=<?php echo $voucher->merchent?>&value=<?php echo $voucher->value?>&expired=<?php echo $voucher->expired?>"><span class="icon-edit"></span>Gia hạn</a>
					</td>
					<td><?php if ($voucher->type == 2) 
								echo "OneCard";
							else 
								echo "NCC";
					?></td>
					<td><?php echo $total; ?></td>
					<td><?php echo $exported?></td>
					<td><?php echo $used?></td>
					<td><?php echo $available?></td>
				</tr>
			 <?php }}else {
				 echo "Không có dữ liệu cho nhà cung cấp này";
			 }?>
			<tr style="font-weight:bold;">
				<td></td>
				<td style="border-left: 0;"></td>
				<td style="border-left: 0;"></td>
				<td style="border-left: 0;"></td>
				<td style="border-left: 0;"></td>
				<td>Tổng</td>
				<td><?php echo $total_total?></td>
				<td><?php echo $total_exported?></td>
				<td><?php echo $total_used?></td>
				<td><?php echo $total_available?></td>
			</tr>
		</table>	
		 
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
						filename: "Voucher_report_<?php echo date("d-m-Y")?>"
					});
				}
			});


		</script>
	</div>
	

<!-- UPLOAD VOUCHER TỪ FILE EXCEL -->
	<div class="modal hide fade" id="modal-test">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Upload Voucher từ file excel</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_inventory&view=vouchers" method="post" enctype="multipart/form-data" id="importForm">
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
					<p>Sheet</p>
					<input type="number" name="sheet" value="1"/>
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
<?php }?>