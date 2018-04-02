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

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base() . '/media/com_onecard/css/item.css');

$codes = JRequest::getVar('codes');

if ($codes) {
	$codes = str_replace(",","','",$codes);
	$codes = str_replace(" ","",$codes);
	$codes = "'".$codes."'";
	$db = JFactory::getDbo();
	
	$query = $db->getQuery(true);
	
	// Fields to update.
	$fields = array(
		$db->quoteName('status') . ' = 2',
		$db->quoteName('exported_id') . ' = 27'
	);
	
	// Conditions for which records should be updated.
	$conditions = array(
		$db->quoteName('state') . ' = 1', 
		$db->quoteName('code') . ' IN (' . $codes.')'
	);
	
	$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);
	
	$db->setQuery($query);
	
	$result = $db->execute();
	echo "<p style='    word-wrap: break-word;'>Đã chuyển trạng thái các mã: ".$codes."</p>";
}
?>
<form action="" method="post">
	<label>Nhập các mã cần chuyển trạng thái, cách nhau bằng dấu phẩy (CODE123,CODE456...)</label>
	<textarea name="codes" row="10" style="width:100%; height: 200px;"></textarea> <br/>
	<button class="btn btn-info">Trạng thái đã xuất</button>
	<input type="hidden" name="option" value="com_onecard"/>
	<input type="hidden" name="view" value="checkcode"/>
	
</form>