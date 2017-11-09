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

jimport('joomla.application.component.view');

/**
 * View class for a list of Onecard.
 *
 * @since  1.6
 */
class OnecardViewCodes extends JViewLegacy
{ 
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }

        OnecardHelpersOnecard::addSubmenu('codes');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = OnecardHelpersOnecard::getActions();

        JToolBarHelper::title(JText::_('COM_ONECARD_TITLE_CODES'), 'codes.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/code';

        if (file_exists($formPath))
        {
            if ($canDo->get('core.create'))
            {
                JToolBarHelper::addNew('code.add', 'JTOOLBAR_NEW');
               // JToolbarHelper::custom('codes.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit') && isset($this->items[0]))
            {
                JToolBarHelper::editList('code.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state'))
        {
            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::custom('codes.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('codes.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            }
            elseif (isset($this->items[0]))
            {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'codes.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('codes.archive', 'JTOOLBAR_ARCHIVE');
            }

            if (isset($this->items[0]->checked_out))
            {
                JToolBarHelper::custom('codes.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state))
        {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
            {
                JToolBarHelper::deleteList('', 'codes.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            }
            elseif ($canDo->get('core.edit.state'))
            {
                JToolBarHelper::trash('codes.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_onecard');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_onecard&view=codes');

        $this->extra_sidebar = '';
        JHtmlSidebar::addFilter(

            JText::_('JOPTION_SELECT_PUBLISHED'),

            'filter_published',

            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

        );
		
		
		
		//Filter for the field created_by
		$this->extra_sidebar .= '<div class="other-filters">';
		$this->extra_sidebar .= '<small><label for="filter_created_by">'.JText::_('COM_ONECARD_FORM_LBL_code_CREATED_BY').'</label></small>';
		$this->extra_sidebar .= JHtmlList::users('filter_created_by', $this->state->get('filter.created_by'), 1, 'onchange="this.form.submit();"');
		$this->extra_sidebar .= '</div>';
		$this->extra_sidebar .= '<hr class="hr-condensed">';
		


        //Filter for the field foreign;
        jimport('joomla.form.form');
        $options = array();
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        $form = JForm::getInstance('com_onecard.code', 'code');

        $field = $form->getField('voucher');

        $query = $form->getFieldAttribute('filter_voucher','query');
        $translate = $form->getFieldAttribute('filter_voucher','translate');
        $key = $form->getFieldAttribute('filter_voucher','key_field');
        $value = $form->getFieldAttribute('filter_voucher','value_field');

        // Get the database object.
        $db = JFactory::getDbo();

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items))
        {
            foreach ($items as $item)
            {
                if ($translate == true)
                {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                }
                else
                {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        JHtmlSidebar::addFilter(
            '$voucher',
            'filter_voucher',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.voucher')),
            true
        );
		


		//Filter for the field with_module
		$select_label = JText::_("COM_ONECARD_SELECT_LBL_CODE_STATUS");
		$options = array();
			$options[0] = new stdClass();
			$options[0]->value = "1";
			$options[0]->text = "Trong kho";
			$options[1] = new stdClass();
			$options[1]->value = "2";
			$options[1]->text = "Đã xuất";
			$options[2] = new stdClass();
			$options[2]->value = "3";
			$options[2]->text = "Đã sử dụng";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_status',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.status'), true)
		);
		


		
		

		$this->extra_sidebar .= '<br />';
		$this->extra_sidebar .= '<br />';
		$this->extra_sidebar .= '<br />';
		$this->extra_sidebar .= '<br />';	
    }

    /**
     * Method to order fields 
     *
     * @return void 
     */
    protected function getSortFields()
    {
        return array(
            'a.`id`' => JText::_('JGRID_HEADING_ID'),
            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
            'a.`state`' => JText::_('JSTATUS'),
			'a.`id`' => JText::_('COM_ONECARD_FORM_LBL_code_ID'),
'a.`state`' => JText::_('COM_ONECARD_FORM_LBL_code_STATE'),
'a.`ordering`' => JText::_(''),
'a.`created_by`' => JText::_('COM_ONECARD_FORM_LBL_code_CREATED_BY'),
'a.`modified_by`' => JText::_('COM_ONECARD_FORM_LBL_code_MODIFIED_BY'),
'a.`code`' => JText::_('COM_ONECARD_FORM_LBL_code_CODE'),
'a.`barcode`' => JText::_('COM_ONECARD_FORM_LBL_code_BARCODE'),
'a.`expired`' => JText::_('COM_ONECARD_FORM_LBL_code_EXPIRED'),
'a.`voucher`' => JText::_('COM_ONECARD_FORM_LBL_code_VOUCHER'),
'a.`status`' => JText::_('COM_ONECARD_FORM_LBL_code_STATUS'),

        );
    }
}
