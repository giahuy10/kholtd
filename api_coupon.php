<?php
$code = JRequest::getVar('code');
$id = JRequest::getVar('id');
if ($code == "list") {
    $db = JFactory::getDbo();
    
    // Create a new query object.
    $query = $db->getQuery(true);
    
    // Select all records from the user profile table where key begins with "custom.".
    // Order it by the ordering field.
    $query->select($db->quoteName(array('id', 'title', 'expired', 'description')));    
    $query->from($db->quoteName('#__onecard_voucher'));
    $query->where($db->quoteName('discount_type') . ' = '.$id);
    $query->where($db->quoteName('state') . ' = 1');
    $query->where($db->quoteName('expired') . ' > NOW() ');
    $query->order('expired ASC');
    
    // Reset the query using our newly populated query object.
    $db->setQuery($query);
    
    // Load the results as a list of stdClass objects (see later for more options on retrieving data).
    $results = $db->loadObjectList();
    foreach ($results as $x => &$coupon) {
        preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $coupon->description, $image);
        $coupon->intro_image =  $image['src']; 
        $db = JFactory::getDbo();
        
        // Create a new query object.
        $query = $db->getQuery(true);
        
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__onecard_code'));
        $query->where($db->quoteName('voucher') . ' = '.$coupon->id);
        $query->where($db->quoteName('state') . ' = 1');
        $query->where($db->quoteName('status') . ' = 1 ');
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $db->execute();
        $coupon->available_code = $db->getNumRows();
    }
    header('Content-Type: application/json');
    echo json_encode(array('status' => 1, 'message' => 'success', 'data' => $results));
    exit();
} else {
    $db = JFactory::getDbo();
    
    // Create a new query object.
    $query = $db->getQuery(true);
    
    // Select all records from the user profile table where key begins with "custom.".
    // Order it by the ordering field.
    $query->select($db->quoteName(array('code', 'barcode', 'serial')));    
    $query->from($db->quoteName('#__onecard_code'));
    $query->where($db->quoteName('voucher') . ' = '.$id);
    $query->where($db->quoteName('state') . ' = 1');
    $query->where($db->quoteName('status') . ' = 1 ');
   
    
    // Reset the query using our newly populated query object.
    $db->setQuery($query,0,1);
    
    // Load the results as a list of stdClass objects (see later for more options on retrieving data).
    $results = $db->loadObject();
    if ($results) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 1, 'message' => 'success', 'data' => $results));
        exit();
    }else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => -1, 'message' => 'Out of Code', 'data' => $results));
        exit();
    }
    
}