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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_gift'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Gift', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('GiftHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gift.php');

$controller = JControllerLegacy::getInstance('Gift');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
