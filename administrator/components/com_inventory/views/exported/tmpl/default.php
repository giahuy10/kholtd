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


// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$merchants = InventoryHelper::get_merchants();


$sortFields = $this->getSortFields();
$jinput = JFactory::getApplication()->input;

?>
<form action="<?php echo JRoute::_('index.php?option=com_inventory&view=exported'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

            <?php //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="voucherList">
				<thead>
				<tr>
					<th></th>
					<th>Nhân viên</th>
					<th>Thời gian</th>
					<th>Số mã</th>
					<th>Giá trị</th>
					<th>NCC</th>
					<th>Sự kiện</th>
					<th>Hạn sử dụng</th>
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
					
					?>
					<tr class="row<?php echo $i % 2; ?>">


										<td>

					<?php echo $item->id; ?>
				</td>				<td>
				
					<?php echo JFactory::getUser($item->used_id)->name ?>
			

				</td>				<td>

					<?php echo $item->created; ?>
				</td>				<td>

					<?php echo $item->number; ?>
				</td>				<td>

					<?php echo $item->value; ?>
				</td>				<td>

					<?php echo InventoryHelper::get_merchant_name($item->merchant); ?>
				</td>
				<td>

					<?php echo $item->event; ?>
				</td>
				<td>

					<?php echo $item->expired; ?>
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



<!-- XUẤT VOUCHER -->	
<div class="modal hide fade" id="modal-export">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Xuất voucher</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_inventory&view=vouchers" method="post" >
					<p>Số lượng voucher</p>
					<input type="number" name="number" id="number" class="inputbox" />
					<p>Giá trị</p>
					<input type="number" name="value" id="value" class="inputbox" />
					<p>Nhà cung cấp</p>
					<select name="merchant" required>
						<option value="">--Chọn nhà cung cấp--</option>
						<?php foreach ($merchants as $option) {?>
							<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
						<?php }?>
					</select>
					
					<p>Sự kiện</p>
					<input type="text" name="event" id="event" class="inputbox" />
					
					<p>Hạn sử dụng</p>
					<input type="date" name="expired" id="expired" class="inputbox" />
					
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
	

