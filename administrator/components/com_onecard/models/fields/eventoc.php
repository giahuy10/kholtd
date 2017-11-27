<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

class JFormFieldEventoc extends JFormFieldList {

	protected $type = 'Eventoc';

    public function getInput() {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $json = file_get_contents('https://onecard.ycar.vn/api.php?act=item&code=all', false, stream_context_create($arrContextOptions));      
        $data = json_decode($json);
       // $app = JFactory::getApplication();
        $id = JRequest::getVar('id'); //country is the dynamic value which is being used in the viewv
        if ($id) {
            $db = JFactory::getDbo();
            
            // Create a new query object.
            $query = $db->getQuery(true);
            
            // Select all records from the user profile table where key begins with "custom.".
            // Order it by the ordering field.
            $query->select($db->quoteName('eventoc'));
            $query->from($db->quoteName('#__onecard_voucher'));
            $query->where($db->quoteName('id') . ' = '. $id);
            
            
            // Reset the query using our newly populated query object.
            $db->setQuery($query);
            
            // Load the results as a list of stdClass objects (see later for more options on retrieving data).
            $current_id = $db->loadResult();
            $current_id = explode(",", $current_id);
        }
        

        $events = $data->data->event;
      /*  foreach ($events as $item) {
            $options[] = JHtml::_('select.option', $item->id, $item->title);
        };
        $drawField = '';
        $drawField .= '<select name="' . $this->name . '" id="' . $this->id . '" class="inputbox" size="10" multiple="multiple">';
        $drawField .= JHtml::_('select.options', $options, 'value', 'text', $current_id, true);
        $drawField .= '</select>';
        return $drawField;
        */
       $html = '<select id="'.$this->id.'" name="'.$this->name. '" class="inputbox" size="10" multiple="multiple">';
                $html.='<option value="">Chọn sự kiện trên OneCard</option>';
                foreach($events as $row){
                   if (in_array($row->id, $current_id)) {
                       $selected = " selected";
                   }else {
                       $selected = "";
                   }
                    $html.= '<option value="'.$row->id.'" '.$selected.' >'. $row->id ."-".$row->title.'</option>';

                }
                $html.='</select>';
		return $html;
	}
}