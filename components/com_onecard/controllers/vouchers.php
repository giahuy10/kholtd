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

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Vouchers list controller class.
 *
 * @since  1.6
 */
class OnecardControllerVouchers extends OnecardController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Vouchers', $prefix = 'OnecardModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
