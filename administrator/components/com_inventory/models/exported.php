<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Inventory
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Inventory records.
 *
 * @since  1.6
 */
class InventoryModelExported extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'state', 'a.`state`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'code', 'a.`code`',
				'value', 'a.`value`',
				'merchent', 'a.`merchent`',
				'expired', 'a.`expired`',
				'event', 'a.`event`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering merchent
		$this->setState('filter.merchent', $app->getUserStateFromRequest($this->context.'.filter.merchent', 'filter_merchent', '', 'string'));

		// Filtering expired
		$this->setState('filter.expired.from', $app->getUserStateFromRequest($this->context.'.filter.expired.from', 'filter_from_expired', '', 'string'));
		$this->setState('filter.expired.to', $app->getUserStateFromRequest($this->context.'.filter.expired.to', 'filter_to_expired', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_inventory');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.code', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__inventory_code_exported` AS a');

		
		// Join over the category 'merchent'
		$query->select('`merchent`.title AS `merchent`');
		$query->join('LEFT', '#__categories AS `merchent` ON `merchent`.id = a.`merchant`');

		

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.code LIKE ' . $search . '  OR  a.event LIKE ' . $search . ' )');
			}
		}


		// Filtering merchent
		$filter_merchent = $this->state->get("filter.merchent");

		if ($filter_merchent !== null && !empty($filter_merchent))
		{
			$query->where("a.`merchent` = '".$db->escape($filter_merchent)."'");
		}

		// Filtering expired
		$filter_expired_from = $this->state->get("filter.expired.from");

		if ($filter_expired_from !== null && !empty($filter_expired_from))
		{
			$query->where("a.`expired` >= '".$db->escape($filter_expired_from)."'");
		}
		$filter_expired_to = $this->state->get("filter.expired.to");

		if ($filter_expired_to !== null  && !empty($filter_expired_to))
		{
			$query->where("a.`expired` <= '".$db->escape($filter_expired_to)."'");
		}
		// Add the list ordering clause.
		$orderCol  = "created";
		$orderDirn = "desc";

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_inventory.exported', 'exported',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();


		return $items;
	}
}
