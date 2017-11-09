<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller = JControllerLegacy::getInstance('Onecard');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
