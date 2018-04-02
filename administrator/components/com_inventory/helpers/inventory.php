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

/**
 * Inventory helper.
 *
 * @since  1.6
 */
class InventoryHelper
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
			JText::_('COM_INVENTORY_TITLE_VOUCHERS'),
			'index.php?option=com_inventory&view=vouchers',
			$vName == 'vouchers'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_INVENTORY_TITLE_CODES'),
			'index.php?option=com_inventory&view=codes',
			$vName == 'codes'
		);

		JHtmlSidebar::addEntry(
			"Nhà cung cấp",
			"index.php?option=com_categories&extension=com_inventory",
			$vName == 'categories'
		);
		JHtmlSidebar::addEntry(
			"Báo cáo",
			"index.php?option=com_inventory&view=report",
			$vName == 'report'
		);
		if ($vName=='categories') {
			JToolBarHelper::title('Inventory: JCATEGORIES (COM_INVENTORY_TITLE_VOUCHERS)');
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
	public static function get_max_id($table_name){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__'.$table_name));
	
		$query->order('id DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,1);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		if ($results) {
			$results++;
			return ($results);
		}
			
		else {
			return 1;
		}
			
	} 
	public static function export_codes ($merchant, $value, $expired, $number){
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('merchent', 'value', 'code','barcode','event','expired', 'exported','type','id')));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('merchent') . ' = '. $merchant);
		$query->where($db->quoteName('value') . ' = '. $value);
		$query->where($db->quoteName('exported') . ' = 3');
		$query->where($db->quoteName('expired') . ' >= '.$db->quote($expired));
		$query->order('expired ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadObjectList();	
		return ($exported);
	}
	public static function get_merchant_import_report($id, $type=NULL) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('COUNT(*) as total, SUM(value) as value, SUM(price) as price');
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('merchent') . ' = '. $id);
		if ($type)
			$query->where($db->quoteName('exported') . ' = '. $type);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}
	public static function get_merchant_export_report($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('COUNT(*) as total, SUM(value) as value, SUM(price) as price');
		$query->from($db->quoteName('#__inventory_voucher_exported'));
		$query->where($db->quoteName('merchent') . ' = '. $id);
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}
	public static function get_merchant_active_report($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('COUNT(*) as total, SUM(value) as value, SUM(price) as price');
		$query->from($db->quoteName('#__inventory_voucher_exported'));
		$query->where($db->quoteName('merchent') . ' = '. $id);
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}
	public static function get_number_code_of_voucher ($voucher_id, $type=NULL) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
	

		$query->where($db->quoteName('created_id') . ' = '. $voucher_id);
		if ($type)
			$query->where($db->quoteName('exported') . ' = '. $type);
		//echo $query->__toString();

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_vouchers ($merchant=NULL,$expired=NULL,$quantity=NULL) {
		
		// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName(array('value', 'merchent', 'event', 'type','expired','price','id')));
			$query->from($db->quoteName('#__inventory_code_created'));
			if ($merchant) {
				$query->where($db->quoteName('merchent') . ' = '. $merchant);
			}
			if ($expired){
				$expired_date = date('Y-m-d', strtotime("+".$expired." days"));
				$query->where($db->quoteName('expired') . ' <= '.$db->quote($expired_date));
			}
			
			//$query->group($db->quoteName(array('value', 'merchent','expired')));	
			$query->order('expired ASC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			return($results);
	} 
	public static function get_merchant_name ($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('title'));
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('id') . ' = '. $id);
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		return ($results);
	}
	public static function get_merchants ($extension){
		$db = JFactory::getDbo();

	// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'title')));
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('extension') . ' = '. $db->quote($extension));
		$query->where($db->quoteName('published') . ' = 1');


		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$merchants = $db->loadObjectList();
		return ($merchants);
	}
	public static function get_code_exported($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher_exported'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('real_expired') . ' = '. $db->quote($expired));
		//echo $query->__toString();

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_code_used($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('exported') . ' = 2');
		$query->where($db->quoteName('expired') . ' = '. $db->quote($expired));
		//echo $query->__toString();

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_code_report($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('expired') . ' = '. $db->quote($expired));
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_events () {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','title')));
		$query->from($db->quoteName('#__content'));
		

		$query->where($db->quoteName('state') . ' = 1');
	
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$codes = $db->loadObjectList();
	
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($codes);
	}
	public static function get_code_to_renew($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('expired') . ' = '. $db->quote($expired));
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$codes = $db->loadObjectList();
	
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($codes);
	}
	public static function check_export_code ($merchant_id, $value, $number) {
		// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__inventory_voucher'));
			$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
			$query->where($db->quoteName('value') . ' = '. $value);
			

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadResult();
			return ($results);
	} 
	public static function check_code ($code) {
	

			// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__inventory_voucher'));
			$query->where($db->quoteName('code') . ' = '. $db->quote($code));
			

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadResult();
			return ($results);
	} 
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
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_inventory';

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

