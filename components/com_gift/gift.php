<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Gift
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Gift', JPATH_COMPONENT);
JLoader::register('GiftController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Gift');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
