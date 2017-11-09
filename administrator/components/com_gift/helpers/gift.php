<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Gift
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Gift helper.
 *
 * @since  1.6
 */
class GiftHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_GIFT_TITLE_ITEMS'),
			'index.php?option=com_gift&view=items',
			$vName == 'items'
		);

		JHtmlSidebar::addEntry(
			JText::_('JCATEGORIES') . ' (' . JText::_('COM_GIFT_TITLE_ITEMS') . ')',
			"index.php?option=com_categories&extension=com_gift",
			$vName == 'categories'
		);
		if ($vName=='categories') {
			JToolBarHelper::title('Gift: JCATEGORIES (COM_GIFT_TITLE_ITEMS)');
		}

	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	 public static function get_stock($quantity) {
		 // Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName(array('title', 'manufacturer', 'quantity')));
			$query->from($db->quoteName('#__gift_item'));
			$query->where($db->quoteName('state') . ' = 1');
			$query->where($db->quoteName('quantity') . ' < '.$quantity);
			$query->order('quantity ASC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			return $results;
	 }
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_gift';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

