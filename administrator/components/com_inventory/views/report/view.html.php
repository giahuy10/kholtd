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
class InventoryViewReport extends JViewLegacy
{
	protected $items;

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
		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		InventoryHelper::addSubmenu('report');

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
	

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_inventory&view=codes');
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
