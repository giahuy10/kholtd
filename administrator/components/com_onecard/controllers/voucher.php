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
	}
}
