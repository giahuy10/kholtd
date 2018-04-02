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
class OnecardViewOrder_vouchers extends JViewLegacy
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

        OnecardHelpersOnecard::addSubmenu('order_vouchers');

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

        JToolBarHelper::title(JText::_('COM_ONECARD_TITLE_ORDER_VOUCHERS'), 'order_vouchers.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/order_voucher';

        if (file_exists($formPath))
        {
            if ($canDo->get('core.create'))
            {
                JToolBarHelper::addNew('order_voucher.add', 'JTOOLBAR_NEW');
                JToolbarHelper::custom('order_vouchers.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
            }

            if ($canDo->get('core.edit') && isset($this->items[0]))
            {
                JToolBarHelper::editList('order_voucher.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state'))
        {
            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::custom('order_vouchers.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('order_vouchers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            }
            elseif (isset($this->items[0]))
            {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'order_vouchers.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state))
            {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('order_vouchers.archive', 'JTOOLBAR_ARCHIVE');
            }

            if (isset($this->items[0]->checked_out))
            {
                JToolBarHelper::custom('order_vouchers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state))
        {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
            {
                JToolBarHelper::deleteList('', 'order_vouchers.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            }
            elseif ($canDo->get('core.edit.state'))
            {
                JToolBarHelper::trash('order_vouchers.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_onecard');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_onecard&view=order_vouchers');

        $this->extra_sidebar = '';
        JHtmlSidebar::addFilter(

            JText::_('JOPTION_SELECT_PUBLISHED'),

            'filter_published',

            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

        );
		
		
		
		//Filter for the field created_by
		$this->extra_sidebar .= '<div class="other-filters">';
		$this->extra_sidebar .= '<small><label for="filter_created_by">'.JText::_('COM_ONECARD_FORM_LBL_order_voucher_CREATED_BY').'</label></small>';
		$this->extra_sidebar .= JHtmlList::users('filter_created_by', $this->state->get('filter.created_by'), 1, 'onchange="this.form.submit();"');
		$this->extra_sidebar .= '</div>';
		$this->extra_sidebar .= '<hr class="hr-condensed">';
		


        //Filter for the field foreign;
        jimport('joomla.form.form');
        $options = array();
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        $form = JForm::getInstance('com_onecard.order_voucher', 'order_voucher');

        $field = $form->getField('order_id');

        $query = $form->getFieldAttribute('filter_order_id','query');
        $translate = $form->getFieldAttribute('filter_order_id','translate');
        $key = $form->getFieldAttribute('filter_order_id','key_field');
        $value = $form->getFieldAttribute('filter_order_id','value_field');

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
            '$order_id',
            'filter_order_id',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.order_id')),
            true
        );
		


        //Filter for the field foreign;
        jimport('joomla.form.form');
        $options = array();
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        $form = JForm::getInstance('com_onecard.order_voucher', 'order_voucher');

        $field = $form->getField('voucher_id');

        $query = $form->getFieldAttribute('filter_voucher_id','query');
        $translate = $form->getFieldAttribute('filter_voucher_id','translate');
        $key = $form->getFieldAttribute('filter_voucher_id','key_field');
        $value = $form->getFieldAttribute('filter_voucher_id','value_field');

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
            '$voucher_id',
            'filter_voucher_id',
            JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.voucher_id')),
            true
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
			'a.`id`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_ID'),
'a.`state`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_STATE'),
'a.`ordering`' => JText::_(''),
'a.`created_by`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_CREATED_BY'),
'a.`modified_by`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_MODIFIED_BY'),
'a.`order_id`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_ORDER_ID'),
'a.`voucher_id`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_VOUCHER_ID'),
'a.`quantity`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_QUANTITY'),
'a.`price`' => JText::_('COM_ONECARD_FORM_LBL_order_voucher_PRICE'),

        );
    }
}
