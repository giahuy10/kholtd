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
        if (task == 'voucher.cancel') {
            Joomla.submitform(task, document.getElementById('voucher-form'));
        }
        else {
            
            if (task != 'voucher.cancel' && document.formvalidator.isValid(document.id('voucher-form'))) {
                
                Joomla.submitform(task, document.getElementById('voucher-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>


<form
    action="<?php echo JRoute::_('index.php?option=com_onecard&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="voucher-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ONECARD_TITLE_VOUCHER', true)); ?>
        
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">				
									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
									<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
									<?php echo $this->form->renderField('ordering'); ?>
									<?php echo $this->form->renderField('checked_out'); ?>
									<?php echo $this->form->renderField('checked_out_time'); ?>

									<?php echo $this->form->renderField('title'); ?>
									<?php echo $this->form->renderField('eventoc'); ?>
									<?php echo $this->form->renderField('eventoc_export'); ?>
									<?php echo $this->form->renderField('max_quantity'); ?>
									<?php if ($this->item->id) {?>
										<span style="color:red;">Số code đã xuất cho OneCard <?php echo OnecardHelper::get_number_of_codes_exported_to_onecard($this->item->id);?></span>
										<?php }?>
									<?php echo $this->form->renderField('type'); ?>
									<?php echo $this->form->renderField('unit'); ?>
									<?php echo $this->form->renderField('discount_type'); ?>
									<?php echo $this->form->renderField('quantity'); ?>
									<?php echo $this->form->renderField('brand'); ?>
									<?php echo $this->form->renderField('value'); ?>
									<?php echo $this->form->renderField('input_price'); ?>
									<?php echo $this->form->renderField('sale_price'); ?>
									<?php echo $this->form->renderField('started'); ?>
									<?php echo $this->form->renderField('expired'); ?>
									<?php echo $this->form->renderField('description'); ?>
									<?php echo $this->form->renderField('created_by'); ?>
									<?php echo $this->form->renderField('modified_by'); ?>
                </fieldset>
            </div>
        </div>
        
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php if ($this->item->id) {?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'advanced', 'Thông tin codes'); ?>
				
							<?php
								$code_need_renew = OnecardHelper::get_code_need_renew($this->item->id);
							$db = JFactory::getDbo();
			
					// Create a new query object.
							$query = $db->getQuery(true);
			
					// Select all records from the user profile table where key begins with "custom.".
					// Order it by the ordering field.
							$query->select('COUNT(*) as total, created, expired, status, input_price');
							$query->from($db->quoteName('#__onecard_code'));
							$query->where($db->quoteName('voucher') . ' = ' . $this->item->id);
							$query->group(array('created', 'input_price','expired'));
							$db->setQuery($query);
					//Onecardhelper::log_sql("get_code_need_renew",$query->__toString());
					// Load the results as a list of stdClass objects (see later for more options on retrieving data).
							$results = $db->loadObjectlist();
							
							?>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Ngày nhập</th>
										<th>Ngày hết hạn</th>
										<th>Giá nhập</th>
										<th>Số lượng</th>
										<th>Đã xuất</th>
										<th>Tồn kho</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($results as $result) {?>
										<tr>
												<td><?php echo date("d-m-Y",strtotime($result->created))?></td>
												<td><?php echo date("d-m-Y", strtotime($result->expired)) ?></td>
												<td><?php echo number_format($result->input_price) ?></td>
												<td><?php echo $result->total ?></td>
												<td><?php $exported = OnecardHelper::get_number_of_voucher($this->item->id,2, $result->input_price, $result->created, $result->expired);
													  echo $exported ?></td>
												<td><?php echo $result->total - $exported ?></td>
											</tr>
									<?php }?>		
									</tbody>
							</table>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php }?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
<!-- UPLOAD VOUCHER TỪ FILE EXCEL -->
	<div class="modal hide fade" id="modal-test">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Upload Voucher từ file excel <a href="<?php echo JURI::root()?>mau-file-upload-voucher-code-cua-NCC3.xlsx">File mẫu</a></h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			
				<div class="row-fluid">
					<div class="12">
						<p>Chọn file </p>
						<input type="file" name="fileToUpload" id="fileToUpload" size="40" class="inputbox" />
						<font color="red">(Max:&nbsp;<?php echo ini_get('upload_max_filesize'); ?>)</font>		<br/>
						Giá nhập cho lần này: <?php echo number_format($this->item->input_price); ?> <span style="color:red; font-weight:bold">* Lưu giá nhập trước khi tạo CODE</span>
					</div> 
				</div>
					
					
					<br/>
					<br/>
					<button class="btn" id="upload_code">Upload code</button>	
				
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>
	</div>
	
<!-- TẠO VOUCHER TỰ ĐỘNG -->	
	<div class="modal hide fade" id="modal-generate">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Tạo code tự động</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			
				<div class="row-fluid">
					<div class="span12">
						
						<p>Số lượng code</p>
						<input type="number" name="number" id="number" class="inputbox" />
						
						<p>Mã sự kiện (XX)</p>
						<input type="text" name="event_code" id="event_code" class="inputbox" />
						<br/>
						Giá nhập cho lần này: <?php echo number_format($this->item->input_price); ?> <span style="color:red; font-weight:bold">* Lưu giá nhập trước khi tạo CODE</span><br/>
						<button class="btn" id="create_code">Tạo code</button>	
						
					</div>
				</div>
					
					
				
					
				
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>
<!-- GIA HAN VOUCHER-->
<div class="modal hide fade" id="modal-renew">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Gia hạn code</h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			
				<div class="row-fluid">
					<div class="span12">
						
						<p>Code (Ngày nhập | Hạn hiện tại | Số lượng tồn)</p>
						<select name="code_need_renew" id="code_need_renew">
							<?php if ($this->item->id) 
								$code_need_renew = OnecardHelper::get_code_need_renew($this->item->id);
								echo "hello";
								var_dump($code_need_renew); 
							foreach ($code_need_renew as $item_renew) {?>
								<option value="<?php echo $item_renew->created?>"><?php echo $item_renew->created." | ".$item_renew->expired." | ".$item_renew->total?></option>
							<?php }?>
						</select>
						
						<p>Hạn sử dụng mới (yyyy-mm-dd)</p>
						<input type="date" name="new_expired" id="new_expired" class="inputbox" />
						<br/>
						<button class="btn" id="renew_code">Gia hạn</button>	
						
					</div>
				</div>
					
					
				
					
				
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div>


<script>
jQuery( document ).ready(function( $ ) {
	// CREATE CODE
	$('#create_code').click(function(){

	   
	var number_code = $('#number').val();
	var event_code = $('#event_code').val();
		$.ajax
		({ 
			url: 'index.php?option=com_onecard&view=ajax&format=raw&type=create_code&voucher_id=<?php echo $this->item->id?>&expired=<?php echo date("Y-m-d",strtotime($this->item->expired))?>&input_price=<?php echo $this->item->input_price;?>',
			data: {"number_code": number_code, "event_code":event_code},
			type: 'post',
			success: function(result)
			{
			   alert (result);
			}
		});
	});

	// Renew CODE
	$('#renew_code').click(function(){

	   
	var code_need_renew = $('#code_need_renew option:selected').val();
	alert(code_need_renew);
	var new_expired = $('#new_expired').val();
		$.ajax
		({ 
			url: 'index.php?option=com_onecard&view=ajax&format=raw&type=renew_code&voucher_id=<?php echo $this->item->id?>',
			data: {"code_need_renew": code_need_renew, "new_expired":new_expired},
			type: 'post',
			success: function(result)
			{
			   alert (result);
			}
		});
	});
	// UPLOAD CODE
	$('#upload_code').click(function(){
		var type_upload = $('select[name=type_upload]').val();
	    var file_data = $('#fileToUpload').prop('files')[0];   
		var form_data = new FormData();      
		form_data.append('file', file_data);		
		$.ajax
		({ 
			url: 'index.php?option=com_onecard&view=ajax&format=raw&type=upload_code&expired=<?php echo date("Y-m-d",strtotime($this->item->expired))?>&voucher_id=<?php echo $this->item->id?>&input_price=<?php echo $this->item->input_price; ?>&type_upload='+type_upload,
			cache: false,
            contentType: false,
            processData: false,
            data: form_data,   
			type: 'post',
			success: function(result)
			{
			   alert (result);
			}
		});
	});
});
</script>