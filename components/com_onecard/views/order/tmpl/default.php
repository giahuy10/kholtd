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

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base() . '/media/com_onecard/css/item.css');
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_onecard');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_onecard')) {
	// $canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

	<div class="item_fields">
	
	
		<table class="table">
				<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_ID'); ?></th><td>
					<?php echo $this->item->id; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_STATE'); ?></th><td><?php echo ($this->item->state == 1) ? JText::_('JPUBLISH') : JText::_('JUNPUBLISH'); ?></td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_CREATED_BY'); ?></th><td>
					<?php echo $this->item->created_by; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_MODIFIED_BY'); ?></th><td>
					<?php echo $this->item->modified_by; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_DATE'); ?></th><td>
					<?php echo $this->item->date; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_USER_ID'); ?></th><td>
					<?php echo $this->item->user_id; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_SUBTOTAL'); ?></th><td>
					<?php echo $this->item->subtotal; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_TOTAL'); ?></th><td>
					<?php echo $this->item->total; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_ONECARD_POINT'); ?></th><td>
					<?php echo $this->item->onecard_point; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_VPOINT'); ?></th><td>
					<?php echo $this->item->vpoint; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_VPOINT_CLIENT'); ?></th><td>
					<?php echo $this->item->vpoint_client; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_ORDER_COUPON_CODE'); ?></th><td>
					<?php echo $this->item->coupon_code; ?>
					
				</td></tr>

		</table>
		
		
	</div>
	<?php if($canEdit && $this->item->checked_out == 0): ?>
		<button type="button" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_onecard&task=order.edit&id='.$this->item->id); ?>';">
			<?php echo JText::_("COM_ONECARD_EDIT_ITEM_ORDER"); ?>
		</button>
	<?php endif; ?>
	<?php if(JFactory::getUser()->authorise('core.delete','com_onecard')):?>
		<button type="button" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_onecard&task=order.remove&id=' . $this->item->id, false, 2); ?>';">
				<?php echo JText::_("COM_ONECARD_DELETE_ITEM_ORDER"); ?>
		</button>
	<?php endif; ?>
	<?php
else:
	echo JText::_('COM_ONECARD_ITEM_NOT_LOADED_ORDER');
endif;
