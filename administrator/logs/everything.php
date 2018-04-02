#
#<?php die('Forbidden.'); ?>
#Date: 2018-03-26 12:55:02 UTC
#Software: Joomla Platform 13.1.0 Stable [ Curiosity ] 24-Apr-2013 00:00 GMT

#Fields: datetime	priority clientip	category	message
2018-03-26T12:55:02+00:00	ERROR 127.0.0.1	database-error	Database query failed (error # 1064): You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'LIMIT 1' at line 3
2018-03-26T12:55:02+00:00	CRITICAL 127.0.0.1	error	Uncaught Exception of type JDatabaseExceptionExecuting thrown. Stack trace: #0 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/joomla/database/driver.php(1642): JDatabaseDriverMysqli->execute()
#1 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/components/com_onecard/helpers/onecard.php(480): JDatabaseDriver->loadObject()
#2 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/components/com_onecard/helpers/onecard.php(764): OnecardHelper::get_voucher_detail('')
#3 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/components/com_onecard/views/export_voucher/tmpl/edit.php(114): OnecardHelper::export_codes_by_voucher('', '2018-05-25', 2, Array)
#4 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/legacy/view/legacy.php(694): include('/Applications/X...')
#5 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/legacy/view/legacy.php(229): JViewLegacy->loadTemplate(NULL)
#6 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/components/com_onecard/views/export_voucher/view.html.php(49): JViewLegacy->display(NULL)
#7 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/legacy/controller/legacy.php(671): OnecardViewExport_voucher->display()
#8 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/components/com_onecard/controller.php(37): JControllerLegacy->display(false, false)
#9 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/legacy/controller/legacy.php(709): OnecardController->display()
#10 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/components/com_onecard/onecard.php(24): JControllerLegacy->execute('get_code')
#11 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/component/helper.php(389): require_once('/Applications/X...')
#12 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/component/helper.php(364): JComponentHelper::executeComponent('/Applications/X...')
#13 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/application/administrator.php(98): JComponentHelper::renderComponent('com_onecard')
#14 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/application/administrator.php(156): JApplicationAdministrator->dispatch()
#15 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/application/cms.php(265): JApplicationAdministrator->doExecute()
#16 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/index.php(51): JApplicationCms->execute()
#17 {main}
2018-03-29T02:20:49+00:00	WARNING 127.0.0.1	jerror	JFolder::create: Could not create folder.Path: /home/ltdf97ac666
2018-03-29T02:20:49+00:00	WARNING 127.0.0.1	jerror	Warning: Failed to move file: /Applications/XAMPP/xamppfiles/temp/phpZx2sQc to /home/ltdf97ac666/public_html/stock/tmp/com_community_std_4.2.5.zip
2018-03-29T02:22:19+00:00	WARNING 127.0.0.1	jerror	JInstaller: :Install: File does not exist /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/components/com_community/all_modules/install_5abc4ddb8b075/language/en-GB/en-GB.mod_community_nearbyevents.ini
2018-03-29T02:22:34+00:00	WARNING 127.0.0.1	jerror	Error loading component: community, Component not found.
2018-03-29T02:22:34+00:00	CRITICAL 127.0.0.1	error	Uncaught Exception of type JComponentExceptionMissing thrown. Stack trace: #0 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/application/administrator.php(98): JComponentHelper::renderComponent('community')
#1 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/application/administrator.php(156): JApplicationAdministrator->dispatch()
#2 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/libraries/cms/application/cms.php(265): JApplicationAdministrator->doExecute()
#3 /Applications/XAMPP/xamppfiles/htdocs/kho/site-stock.ycar.vn-20171109-140036/administrator/index.php(51): JApplicationCms->execute()
#4 {main}
