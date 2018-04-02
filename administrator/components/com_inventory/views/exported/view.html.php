<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Inventory
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Inventory.
 *
 * @since  1.6
 */
 JHtml::_('bootstrap.modal');
class InventoryViewExported extends JViewLegacy
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
		//$this->form  = $this->get('Form');
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
       // $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		InventoryHelper::addSubmenu('vouchers');

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
		$canDo = InventoryHelper::getActions();
		// Get the toolbar object instance
		$toolbar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('joomla.toolbar.popup');

		JToolBarHelper::title(JText::_('COM_INVENTORY_TITLE_VOUCHERS'), 'vouchers.png');
		
			$export_2 = $layout->render(array('name' => 'export2','doTask' => '', 'text' => JText::_('Xuất Excel'), 'class' => 'icon-download'));
			
			$toolbar->appendButton('Custom', $export_2);
		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/voucher';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				//JToolBarHelper::addNew('voucher.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					//JToolbarHelper::custom('vouchers.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				//JToolBarHelper::editList('voucher.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('vouchers.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('vouchers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				//JToolBarHelper::deleteList('', 'vouchers.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				//JToolBarHelper::archiveList('vouchers.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('vouchers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		
			
		

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_inventory');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_inventory&view=vouchers');
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
			'a.`state`' => JText::_('JSTATUS'),
			'a.`code`' => JText::_('COM_INVENTORY_VOUCHERS_CODE'),
			'a.`value`' => JText::_('COM_INVENTORY_VOUCHERS_VALUE'),
			'a.`merchent`' => JText::_('COM_INVENTORY_VOUCHERS_MERCHENT'),
			'a.`expired`' => JText::_('COM_INVENTORY_VOUCHERS_EXPIRED'),
			'a.`event`' => JText::_('COM_INVENTORY_VOUCHERS_EVENT'),
		);
	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
