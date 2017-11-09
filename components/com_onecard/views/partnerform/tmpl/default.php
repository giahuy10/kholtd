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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_onecard', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base() . '/media/com_onecard/css/form.css');
$doc->addScript(JUri::base() . '/media/com_onecard/js/form.js');
?>

<script type="text/javascript">
	if (jQuery === 'undefined') {
		document.addEventListener("DOMContentLoaded", function (event) {
			jQuery('#form-partner').submit(function (event) {
				
			});

			
		});
	} else {
		jQuery(document).ready(function () {
			jQuery('#form-partner').submit(function (event) {
				
			});

			
		});
	}
</script>

<div class="partner-edit front-end-edit">
	<?php if (!empty($this->item->id)): ?>
		<h1>Edit <?php echo $this->item->id; ?></h1>
	<?php else: ?>
		<h1>Add</h1>
	<?php endif; ?>

	<form id="form-partner"
		  action="<?php echo JRoute::_('index.php?option=com_onecard&task=partner.save'); ?>"
		  method="post" class="form-validate" enctype="multipart/form-data">
		  
		  <?php echo $this->form->renderField('id'); ?>
<?php echo $this->form->renderField('state'); ?>
<?php echo $this->form->renderField('ordering'); ?>
<?php echo $this->form->renderField('checked_out'); ?>
<?php echo $this->form->renderField('checked_out_time'); ?>
<?php echo $this->form->renderField('created_by'); ?>
<?php echo $this->form->renderField('modified_by'); ?>
<?php echo $this->form->renderField('title'); ?>
<?php echo $this->form->renderField('description'); ?>

		  
		  
		  
				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php if(empty($this->item->created_by)){ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

				<?php } ?>
				<?php if(empty($this->item->modified_by)){ ?>
					<input type="hidden" name="jform[modified_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />

				<?php } ?>
		<div class="button-div">
			<?php if ($this->canSave): ?>
				<button type="submit" class="validate">
					<span><?php echo JText::_('JSUBMIT'); ?></span>
				</button>
			<?php endif; ?>

			<a href="<?php echo JRoute::_('index.php?option=com_onecard&task=partnerform.cancel'); ?>"
			   title="<?php echo JText::_('JCANCEL'); ?>">
				<?php echo JText::_('JCANCEL'); ?>
			</a>
		</div>

		<input type="hidden" name="option" value="com_onecard"/>
		<input type="hidden" name="task"
			   value="partnerform.save"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
