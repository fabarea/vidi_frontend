<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'vidi_frontend',
	'Pi1',
	'Vidi - List Component'
);

//if (isset($pluginData['flexForm'])) {
//	$TCA['tt_content']['types']['list']['subtypes_addlist']['bobstforms_pi1'] = 'pi_flexform';
//	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
//		'bobstforms_pi1',
//		sprintf('FILE:EXT:vidi_frontend/Configuration/FlexForm/vidi.xml')
//	);
//}