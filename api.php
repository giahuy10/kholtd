<?php 
define('_JEXEC', 1);    

// this file is in a subfolder 'scripts' under the main joomla folder
define('JPATH_BASE', realpath(dirname(__FILE__) . '/'));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Create the Application
$app = JFactory::getApplication('site');

    $act = JRequest::getVar('act');
    
    include_once (JPATH_SITE.'/api_'.$act.'.php');
    