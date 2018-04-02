     <form name="importForm" action="index.php?option=com_update_vm" method="post" enctype="multipart/form-data">
					<p>Chọn file </p>
					<input type="file" name="fileToUpload" id="excel_file" size="40" class="inputbox" />
					<font color="red">(Max:&nbsp;<?php echo ini_get('upload_max_filesize'); ?>)</font>
					
					<p>Ngày nhập </p>
					<input type="date" name="created" value=""/><br/>
					<input type="submit" name="import" value="Update" class="btn"/>	
					<input type="hidden" name="option" value="com_update_vm" />
					<input type="hidden" name="controller" value="importexport" />
					<input type="hidden" name="task" value="upload" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
<?php


 
if(isset($_POST["import"])){
	$target_dir = JPATH_ROOT.'/images/import/';
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
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
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       // echo "<p class='text-success'>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
	   if($imageFileType == "xlsx") {
		   doImport($target_file);
	   }else {
		   doImportxls($target_file);
	   }
		
				
  		if (!unlink($target_file))
			{
		  echo ("Error deleting $target_file");
		  }
		else
		  {
		  //echo ("<p class='text-info'>Deleted $target_file</p>");
		}
    } else {
        echo "<p class='text-danger'>Sorry, there was an error uploading your file.</p>";
    }
}
}	

function doImport($fileExcel){
  		
  				
  		require_once (JPATH_COMPONENT.'/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		
		$rows = $xlsx->rows() ;
		$count_insert=0;
		foreach ($rows as $row) {
			
					$product[$count_insert] = new stdClass();						
					
									
						$product[$count_insert]->order_number = $row[0];
						$product[$count_insert]->date = $_POST['created'];
						import_product($product[$count_insert]);
						$count_insert++;
						
		}

					
					
				//echo "<pre>";
								//var_dump ($product);
						//	echo "</pre>";
						
							
					
					
						
							
					
	
			echo "<h3 class='text-success'>Đã nhập ".$count_insert." mã đơn hàng</h3>";
		


		
	}	
function doImportxls($fileExcel){
  		
  				
  		$rowSaved = 0;
  		$rowError = 0;
  		$rowErrorList = "";

  

	  		require_once (JPATH_COMPONENT.'/reader.php');
	  		// ExcelFile($filename, $encoding);
			$data = new Spreadsheet_Excel_Reader();				
			// Set output Encoding.
			$data->setOutputEncoding('utf-8');
			$data->read($fileExcel);
					
			
				
				
				$user = JFactory::getUser();
				$count_insert= 0;
				$count_update=0;
				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
					$product[$i] = new stdClass();						$count_insert++;
									
						$product[$i]->order_number = $data->sheets[0]['cells'][$i][1];
						$product[$i]->date = $_POST['created'];
				
					

					
					
				//echo "<pre>";//
							//	var_dump ($product[$i]);
							//echo "</pre>";
						
							
					import_product($product[$i]);
					
						
							
					}
			
	
			echo "<h3 class='text-success'>Đã nhập ".$count_insert." mã đơn hàng</h3>";
		


		
	}				
function import_product($data) {
	// Create and populate an object.
	 
	// Insert the object into the user profile table.
	$result = JFactory::getDbo()->insertObject('#__vm_ordered', $data);
} 
   
?>
                            
                            
                            