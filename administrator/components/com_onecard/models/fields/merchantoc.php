<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldMerchantoc extends JFormFieldList {

	protected $type = 'Merchantoc';

    public function getInput() {
        $json = file_get_contents('https://onecard.ycar.vn/api.php?act=merchant&code=get-list');      
        $data = json_decode($json);
       // $app = JFactory::getApplication();
        $id = JRequest::getVar('id'); //country is the dynamic value which is being used in the viewv
        if ($id) {
        $db = JFactory::getDbo();
        
        // Create a new query object.
        $query = $db->getQuery(true);
        
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select($db->quoteName('merchantoc'));
        $query->from($db->quoteName('#__onecard_brand'));
        $query->where($db->quoteName('id') . ' = '. $id);
        
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $current_id = $db->loadResult();
        }
        $events = $data->data;
        $html = '<select id="'.$this->id.'" name="'.$this->name.'">';
                $html.='<option value="">Chon Merchant OC</option>';
                foreach($events as $row){
                   if ($current_id == $row->id) {
                       $selected = " selected";
                   }else {
                       $selected = "";
                   }
                    $html.= '<option value="'.$row->id.'" '.$selected.' >'.$row->title.'</option>';

                }
                $html.='</select>';
		return $html;
	}
}