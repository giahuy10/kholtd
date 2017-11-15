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

jimport('joomla.application.component.controllerform');

/**
 * Table controller class.
 *
 * @since  1.6
 */
class OnecardControllervoucher extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'vouchers';
		parent::__construct();
	}
	public static function delete_record ($table,  $value) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

	// delete all custom keys for user 1001.
		$conditions = array(
			$db->quoteName('voucher') . ' = '. $value
		);

		$query->delete($db->quoteName('#__'. $table));
		$query->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();
	}
	protected function postSaveHook(JModelLegacy $model, $validData=array()){
		$user = JFactory::getUser();
		
		if (!empty($validData['sale_price']))
		{
			$tupel = new stdClass;
			//$tupel->created_by = $user->id;
			$tupel->voucher = (int) $validData['id'];
			foreach ($validData['sale_price'] as $tmp)
			{				
				$tupel->partner= $tmp['partner'];
			
				$tupel->price= $tmp['price'];
				
				$result = JFactory::getDbo()->insertObject('#__onecard_voucher_price', $tupel);
				
					
			}
		}
		if (!empty($validData['eventoc'])) {
			$eventoc = new stdClass;
			$eventoc->voucher = (int)$validData['id'];
			self::delete_record("onecard_voucher_event", $eventoc->voucher);
			
			//$tupel->created_by = $user->id;
			
			foreach ($validData['eventoc'] as $tmp) {
				$eventoc->event = $tmp;
				$result = JFactory::getDbo()->insertObject('#__onecard_voucher_event', $eventoc);
			}
		}
	}
}
