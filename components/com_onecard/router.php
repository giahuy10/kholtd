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

/**
 * Route builder
 *
 * @param   array  &$query  A named array
 *
 * @return    array
 */
function OnecardBuildRoute(&$query)
{
	$segments = array();
	$view     = null;

	if (isset($query['task']))
	{
		$taskParts  = explode('.', $query['task']);
		$segments[] = implode('/', $taskParts);
		$view       = $taskParts[0];
		unset($query['task']);
	}

	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		$view       = $query['view'];
		unset($query['view']);
	}

	if (isset($query['id']))
	{
		if ($view !== null)
		{
			$segments[] = $query['id'];
		}
		else
		{
			$segments[] = $query['id'];
		}

		unset($query['id']);
	}

	return $segments;
}

/**
 * Converts URL segments into query variables.
 *
 * @param   array  $segments  A named array
 *
 * Formats:
 *
 * index.php?/onecard/task/id/Itemid
 *
 * index.php?/onecard/id/Itemid
 *
 * @return array
 */
function OnecardParseRoute($segments)
{
	$vars = array();
	JLoader::register('OnecardFrontendHelper', JPATH_SITE . '/components/com_onecard/helpers/onecard.php');

	// View is always the first element of the array
	$vars['view'] = array_shift($segments);
	$model        = OnecardFrontendHelper::getModel($vars['view']);

	while (!empty($segments))
	{
		$segment = array_pop($segments);

		// If it's the ID, let's put on the request
		if (is_numeric($segment))
		{
			$vars['id'] = $segment;
		}
		else
		{
			$vars['task'] = $vars['view'] . '.' . $segment;
		}
	}

	return $vars;
}
