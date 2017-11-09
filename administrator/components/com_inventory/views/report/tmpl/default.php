<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Inventory
 * @author     sugar lead <anjakahuy@gmail.com>
 * @copyright  2017 sugar lead
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport( 'joomla.form.form' );
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_inventory/assets/css/inventory.css');
$document->addStyleSheet(JUri::root() . 'media/com_inventory/css/list.css');
$document->addScript(JUri::root() . 'administrator/components/com_inventory/assets/js/jquery.table2excel.js');


$user      = JFactory::getUser();
$userId    = $user->get('id');
$jinput = JFactory::getApplication()->input;

$merchant = $jinput->get('merchant',0);
$bought = InventoryHelper::get_merchant_import_report($merchant);
$available = InventoryHelper::get_merchant_import_report($merchant,3);
$exported = InventoryHelper::get_merchant_export_report($merchant);

?>
<form class="form-inline" action="index.php?option=com_inventory&view=report" method="post">
		  <select name="merchant" class="input-large" onchange="this.form.submit()">
				<?php $merchants = InventoryHelper::get_merchants('com_inventory'); ?>
				<option value="">--Chọn nhà cung cấp--</option>
						<?php foreach ($merchants as $option) {?>
							<option value="<?php echo $option->id?>"><?php echo $option->title?></option>
						<?php }?>
		  </select>
	
		</form>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<div class="row-fluid">
			<div class="span6">
				<div id="voucher_report" style="width: 100%; height: 500px;"></div>
			</div>
			<div class="span6">
				<div id="revenue_report" style="width: 100%; height: 500px;"></div>
			</div>
		</div>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Đã bán (<?php echo $exported->total?>)',     <?php echo $exported->total?>],
          ['Tồn kho ( <?php echo $available->total?>)',     <?php echo $available->total?>]
          
        ]);

        var options = {
          title: 'Báo cáo số lượng voucher (<?php echo $bought->total?>)'
        };

        var chart = new google.visualization.PieChart(document.getElementById('voucher_report'));

        chart.draw(data, options);
      }
    </script>
	<script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['2017', 'Mua', 'Đã bán', 'Tồn'],
          ['August', <?php echo $bought->price?>, <?php echo $exported->price?>, <?php echo $available->price?>]
       
        ]);

        var options = {
          chart: {
            title: 'Báo cáo doanh số',
            //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('revenue_report'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
	
