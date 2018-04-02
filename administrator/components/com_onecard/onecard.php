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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_onecard'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}


// Include dependencies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Onecard');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();