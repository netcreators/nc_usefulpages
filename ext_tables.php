<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY, // Note: Vendor name ('Netcreators.') is only added for @see \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin()!
	'Ncusefulpages',
	'Useful Pages'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Rating: Useful Pages');

