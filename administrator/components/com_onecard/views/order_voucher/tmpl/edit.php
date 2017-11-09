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
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {

    });

    Joomla.submitbutton = function (task) {
        if (task == 'order_voucher.cancel') {
            Joomla.submitform(task, document.getElementById('order_voucher-form'));
        }
        else {
            
            if (task != 'order_voucher.cancel' && document.formvalidator.isValid(document.id('order_voucher-form'))) {
                
                Joomla.submitform(task, document.getElementById('order_voucher-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_onecard&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="order_voucher-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ONECARD_TITLE_ORDER_VOUCHER', true)); ?>
        
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">
				
				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
<?php echo $this->form->renderField('ordering'); ?>
<?php echo $this->form->renderField('checked_out'); ?>
<?php echo $this->form->renderField('checked_out_time'); ?>
<?php echo $this->form->renderField('created_by'); ?>
<?php echo $this->form->renderField('modified_by'); ?>
<?php echo $this->form->renderField('order_id'); ?>
<?php echo $this->form->renderField('voucher_id'); ?>
<?php echo $this->form->renderField('quantity'); ?>
<?php echo $this->form->renderField('price'); ?>


                   
                </fieldset>
            </div>
        </div>
        
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
