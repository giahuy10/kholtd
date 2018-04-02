<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.ncc.modelform');
jimport('joomla.event.dispatcher');

use Joomla\Utilities\ArrayHelper;
/**
 * Onecard model.
 *
 * @since  1.6
 */
class OnecardModelNccForm extends JModelForm
{
	private $item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since  1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('com_onecard');

		// Load state from the request userState on edit or from the passed variable on default
		if (JFactory::getApplication()->input->get('layout') == 'edit')
		{
			$id = JFactory::getApplication()->getUserState('com_onecard.edit.ncc.id');
		}
		else
		{
			$id = JFactory::getApplication()->input->get('id');
			JFactory::getApplication()->setUserState('com_onecard.edit.ncc.id', $id);
		}

		$this->setState('ncc.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('ncc.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return Object|boolean Object on success, false on failure.
	 *
	 * @throws Exception
	 */
	public function &getData($id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('ncc.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table !== false && $table->load($id))
			{
				$user = JFactory::getUser();
				$id   = $table->id;
				$canEdit = $user->authorise('core.edit', 'com_onecard') || $user->authorise('core.create', 'com_onecard');

				if (!$canEdit && $user->authorise('core.edit.own', 'com_onecard'))
				{
					//$canEdit = $user->id == $table->created_by;
				}

				if (!$canEdit)
				{
					throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
				}

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->item = ArrayHelper::toObject($properties, 'JObject');
			}
		}

		return $this->item;
	}

	/**
	 * Method to get the table
	 *
	 * @param   string  $type    Name of the JTable class
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $config  Optional configuration array for JTable object
	 *
	 * @return  JTable|boolean JTable if found, boolean false on failure
	 */
	public function getTable($type = 'Ncc', $prefix = 'OnecardTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/nccs/com_onecard/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get an item by alias
	 *
	 * @param   string  $alias  Alias string
	 *
	 * @return int Element id
	 */
	public function getItemIdByAlias($alias)
	{
		$table = $this->getTable();

		$table->load(array('alias' => $alias));

		return $table->id;
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('ncc.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('ncc.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_onecard.ncc', 'nccform', array(
			'control'   => 'jform',
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
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_onecard.edit.ncc.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function save($data)
	{
		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('ncc.id');
		$state = (!empty($data['state'])) ? 1 : 0;
		$user  = JFactory::getUser();

		if ($id)
		{
			// Check the user can edit this item
			$authorised = $user->authorise('core.edit', 'com_onecard') || $authorised = $user->authorise('core.edit.own', 'com_onecard');
		}
		else
		{
			// Check the user can create new items in this section
			$authorised = $user->authorise('core.create', 'com_onecard');
		}

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$table = $this->getTable();

		if ($table->save($data) === true)
		{
			return $table->id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete data
	 *
	 * @param   array  $data  Data to be deleted
	 *
	 * @return bool|int If success returns the id of the deleted item, if not false
	 *
	 * @throws Exception
	 */
	public function delete($data)
	{
		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('ncc.id');

		if (JFactory::getUser()->authorise('core.delete', 'com_onecard') !== true)
		{
			throw new Exception(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$table = $this->getTable();

		if ($table->delete($data['id']) === true)
		{
			return $id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check if data can be saved
	 *
	 * @return bool
	 */
	public function getCanSave()
	{
		$table = $this->getTable();

		return $table !== false;
	}
	
}
