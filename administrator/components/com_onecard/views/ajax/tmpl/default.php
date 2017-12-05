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


$user       = JFactory::getUser();

$type = JRequest::getVar('type');
$voucher_id = JRequest::getVar('voucher_id');
$expired = JRequest::getVar('expired');
$input_price = JRequest::getVar('input_price');
if ($type == "create_code") { // TẠO CODE TỰ ĐỘNG
	$number_code = JRequest::getVar('number_code');
	$event_code = JRequest::getVar('event_code');
	$virtual_code = JRequest::getVar('virtual_code');
	
	
	$k = 0;
	while ($k < $number_code) {

		$code = "O".$event_code.mt_rand(100000, 999999);
		$check = OnecardHelper::check_code($code);
		if (!$check) {
			$k++;
			$product[$k] = new stdClass();						
					
						$product[$k]->state = 1 ;			
						$product[$k]->code = $code;
						$product[$k]->created = date("Y-m-d");
						$product[$k]->input_price = $input_price;
						$product[$k]->expired = $expired;
						$product[$k]->voucher = $voucher_id;
						$product[$k]->status = 1;
						$product[$k]->type = 2;
						$product[$k]->virtual_code = $virtual_code;
						$product[$k]->created_by = $user->id;
					//	echo "<pre>";
						//	var_dump ($product[$k]);
					//	echo "</pre>";
			OnecardHelper::import_product($product[$k],'onecard_code');			
		}
	}
	echo $k.' code được tạo';
	
}
if ($type == "upload_code") { // UPLOAD CODE TỪ FILE EXCEL
		$type_upload = JRequest::getVar('type_upload');
		$target_dir = JPATH_ROOT.'/images/import/';
		$target_file = $target_dir . date('m-d-Y_his').basename($_FILES["file"]["name"]);
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
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
			   // echo "<p class='text-success'>The file ". basename( $_FILES["file"]["name"]). " has been uploaded.</p>";
			   
			   if($imageFileType == "xlsx") {
				   
				   OnecardHelper::doImport($target_file, $voucher_id, $type_upload, $expired, $input_price);
				   
			   }else {
				   //doImportxls($target_file);
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
if ($type == "renew_code") { // RENEW CODE
	$code_need_renew = JRequest::getVar('code_need_renew');
	$new_expired = JRequest::getVar('new_expired');
	$db = JFactory::getDbo();
	
	$query = $db->getQuery(true);
	
	// Fields to update.
	$fields = array(
		$db->quoteName('expired') . ' = ' . $db->quote($new_expired),
		$db->quoteName('renewed') . ' = 1'
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('voucher') . ' = '.$voucher_id, 
		$db->quoteName('created') . ' = ' . $db->quote($code_need_renew)
	);
	
	$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);
	
	$db->setQuery($query);
	
	$result = $db->execute();
	//echo $query->__toString();
	echo "Gia hạn thành công";
}