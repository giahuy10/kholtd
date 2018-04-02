<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_logged
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
$warning_1 = $params->get('warning_1',  45);
$warning_2 = $params->get('warning_2',  30);
$voucher_out_of_stock = $params->get('voucher_out_of_stock',  10);
$item_out_of_stock = $params->get('item_out_of_stock',  10);
require_once (JPATH_ADMINISTRATOR.'/components/com_inventory/helpers/inventory.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_gift/helpers/gift.php');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>
<?php $merchants = InventoryHelper::get_merchants("com_inventory");?>
 <a class="btn btn-default" id="export-merchant_list"><span class="icon-out-2 small"></span> Xuất danh sách NCC</a>
<table id="merchant_list" style="display:none;">
	<tr>
		<td>NCC</td>
		<td>ID</td>
	</tr>
	<?php foreach ($merchants as $ncc) {?>
		<tr>
			<td><?php echo $ncc->title?></td>
			<td><?php echo $ncc->id?></td>
		</tr>
	<?php }?>
</table>

<h2 style="color:red">Cảnh báo</h2>
<div class="row-fluid">
	<div class="span6">
		<h2>Voucher sắp hết hạn trong <?php echo $warning_2?> ngày</h2>
		 <a class="btn btn-default" id="export-warning_2">Xuất file Excel</a>
		<table class="table table-bordered text-center" id="warning_2" data-tableName="Test Table 2">
			<thead>
				<tr>
					<th>Nhà cung cấp</th>
					<th>Giá trị</th>
					<th>Sự kiện</th>
					<th>Ngày hết hạn</th>
					<th>Tồn kho</th>
				</tr>
			</thead>
		<?php 
		$merchant=NULL;
		
		$quantity=NULL;
			$warning_items = InventoryHelper::get_vouchers($merchant,$warning_2,$quantity);
			 $total_total=0;
			 $total_exported=0;
			 $total_used=0;
			 $total_available=0;
			 if ($warning_items) {
			foreach ($warning_items as $voucher) {?>
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
					<td><?php echo InventoryHelper::get_merchant_name($voucher->merchent)?></td>
					<td><?php echo number_format($voucher->value)?></td>
					<td><?php echo $voucher->event?></td>
					<td><?php echo $voucher->expired?></td>
				
					
					<td><?php echo $available?></td>
				</tr>
			 <?php }}else {
				 echo "Không có dữ liệu cho nhà cung cấp này";
			 }?>
			<tr style="font-weight:bold;">
				<td></td>
			
				<td style="border-left: 0;"></td>
				<td style="border-left: 0;"></td>
				<td>Tổng</td>
				
				<td><?php echo $total_available?></td>
			</tr>
		</table>	
			
	</div>
	<div class="span6">
		<h2>Voucher sắp hết hạn trong <?php echo $warning_1?> ngày</h2>
		 <a class="btn btn-default" id="export-warning_1">Xuất file Excel</a>
		<table class="table table-bordered text-center" id="warning_1" data-tableName="Test Table 2">
			<thead>
				<tr>
					<th>Nhà cung cấp</th>
					<th>Giá trị</th>
					<th>Sự kiện</th>
					<th>Ngày hết hạn</th>
					<th>Tồn kho</th>
				</tr>
			</thead>
		<?php 
		$merchant=NULL;
		
		$quantity=NULL;
			$warning_items = InventoryHelper::get_vouchers($merchant,$warning_1,$quantity);
			 $total_total=0;
			 $total_exported=0;
			 $total_used=0;
			 $total_available=0;
			 if ($warning_items) {
			foreach ($warning_items as $voucher) {?>
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
					<td><?php echo InventoryHelper::get_merchant_name($voucher->merchent)?></td>
					<td><?php echo number_format($voucher->value)?></td>
					<td><?php echo $voucher->event?></td>
					<td><?php echo $voucher->expired?></td>
				
					
					<td><?php echo $available?></td>
				</tr>
			 <?php }}else {
				 echo "Không có dữ liệu cho nhà cung cấp này";
			 }?>
			<tr style="font-weight:bold;">
				<td></td>
			
				<td style="border-left: 0;"></td>
				<td style="border-left: 0;"></td>
				<td>Tổng</td>
				
				<td><?php echo $total_available?></td>
			</tr>
		</table>	
		
				
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<h2>Voucher số lượng dưới <?php echo $voucher_out_of_stock?></h2>
		 <a class="btn btn-default" id="export-voucher_out_of_stock">Xuất file Excel</a>
		<table class="table table-bordered text-center" id="voucher_out_of_stock" data-tableName="Test Table 2">
			<thead>
				<tr>
					<th>Nhà cung cấp</th>
					<th>Giá trị</th>
					<th>Sự kiện</th>
					<th>Ngày hết hạn</th>
					<th>Tồn kho</th>
				</tr>
			</thead>
		<?php 
		$merchant=NULL;
		
		$quantity=NULL;
			$warning_items = InventoryHelper::get_vouchers();
			 $total_total=0;
			 $total_exported=0;
			 $total_used=0;
			 $total_available=0;
			 if ($warning_items) {
			foreach ($warning_items as $voucher) {?>
				<?php 
						$total = InventoryHelper::get_number_code_of_voucher($voucher->id);
					$total_total+=$total;
					$exported = InventoryHelper::get_number_code_of_voucher($voucher->id,1);
					$total_exported+=$exported;
					$used = InventoryHelper::get_number_code_of_voucher($voucher->id,2);
					$total_used+=$used;
					$available = $total-$exported;
					$total_available+=$available;
					
					if ($available <= $voucher_out_of_stock && $available > 0) {
						$total_available+=$available;
						$total_total+=$total;
						$total_exported+=$exported;
							$total_used+=$used;
				?>
				<tr>
					<td><?php echo InventoryHelper::get_merchant_name($voucher->merchent)?></td>
					<td><?php echo number_format($voucher->value)?></td>
					<td><?php echo $voucher->event?></td>
					<td><?php echo $voucher->expired?></td>
				
					
					<td><?php echo $available?></td>
				</tr>
			<?php }}}else {
				 echo "Không có dữ liệu cho nhà cung cấp này";
			 }?>
			<tr style="font-weight:bold;">
				<td></td>
			
				<td style="border-left: 0;"></td>
				<td style="border-left: 0;"></td>
				<td>Tổng</td>
				
				<td><?php echo $total_available?></td>
			</tr>
		</table>	
		
	</div>
	<div class="span6">
		<h2>Sản phẩm số lượng dưới <?php echo $item_out_of_stock?></h2>
		 <a class="btn btn-default" id="export-item_out_of_stock">Xuất file Excel</a>
		<?php 
			$items = GiftHelper::get_stock($item_out_of_stock);
		
		?>
		<table class="table table-bordered text-center" id="item_out_of_stock" data-tableName="Test Table 2">
			<thead>
				<tr>
					<th>Sản phẩm</th>
					<th>Hãng</th>
					<th>Số lượng</th>
					
				</tr>
			</thead>	
				<?php foreach ($items as $item) {?> 
					<tr>
						<td><?php echo $item->title?></td>
						<td><?php echo InventoryHelper::get_merchant_name($item->manufacturer)?></td>
						<td><?php echo $item->quantity?></td>
					</tr>
				<?php }?>
			</table>
			
	</div>
</div>
<script>
			jQuery(document).ready(function() {
    
				$('#export-item_out_of_stock').on('click', function(e){
					e.preventDefault();
					ResultsToTable('item_out_of_stock','warning_item_quantity');
				});
				$('#export-voucher_out_of_stock').on('click', function(e){
					e.preventDefault();
					ResultsToTable('voucher_out_of_stock','warning_voucher_quantity');
				});
				$('#export-warning_1').on('click', function(e){
					e.preventDefault();
					ResultsToTable('warning_1','warning_voucher_expire_45_day');
				});
				$('#export-warning_2').on('click', function(e){
					e.preventDefault();
					ResultsToTable('warning_2','warning_voucher_expire_30_day');
				});
				$('#export-merchant_list').on('click', function(e){
					e.preventDefault();
					ResultsToTable('merchant_list','merchant_list');
				});
				
				function ResultsToTable(table,name){    
					$("#"+table).table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: name+"_<?php echo date("d-m-Y")?>"
					});
				}
			});


		</script>