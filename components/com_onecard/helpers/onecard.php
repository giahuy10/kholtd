<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */
defined('_JEXEC') or die;

/**
 * Class OnecardFrontendHelper
 *
 * @since  1.6
 */
class OnecardFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_onecard/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_onecard/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'OnecardModel');
		}

		return $model;
	}
}
