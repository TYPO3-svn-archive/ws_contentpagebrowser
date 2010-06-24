<?php

########################################################################
# Extension Manager/Repository config file for ext "ws_contentpagebrowser".
#
# Auto generated 24-06-2010 07:50
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Content with PageBrowser',
	'description' => 'Separate the content of a page with pagebrowser. Shows content of current page and current column or from sys folder.',
	'category' => 'plugin',
	'author' => 'Nikolay Orlenko',
	'author_email' => 'info@web-spectr.com',
	'author_company' => 'Web.Spectr',
	'shy' => '',
	'dependencies' => 'pagebrowse',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.1.1',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '4.2.0-0.0.0',
			'pagebrowse' => '1.1.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:13:{s:9:"ChangeLog";s:4:"acaf";s:10:"README.txt";s:4:"9fa9";s:12:"ext_icon.gif";s:4:"df3f";s:17:"ext_localconf.php";s:4:"04bb";s:14:"ext_tables.php";s:4:"c9b4";s:15:"flexform_ds.xml";s:4:"24c9";s:13:"locallang.xml";s:4:"fdbd";s:16:"locallang_db.xml";s:4:"ca39";s:14:"doc/manual.sxw";s:4:"7e72";s:41:"pi1/class.tx_wscontentpagebrowser_pi1.php";s:4:"7db6";s:17:"pi1/locallang.xml";s:4:"529e";s:39:"static/contentpagebrowser/constants.txt";s:4:"9eb3";s:35:"static/contentpagebrowser/setup.txt";s:4:"69b0";}',
);

?>