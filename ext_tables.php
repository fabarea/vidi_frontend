<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'Fab.vidi_frontend',
		'Pi1',
		'Generic List Component'
	);

	$TCA['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		'vidifrontend_pi1',
		sprintf('FILE:EXT:vidi_frontend/Configuration/FlexForm/VidiFrontend.xml')
	);

}

// Register cache for this extension
//if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['vidi_frontend_cache'])) {
//	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['vidi_frontend_cache'] = array();
//	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['vidi_frontend_cache']['frontend'] = 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend';
//	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['vidi_frontend_cache']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend';
//	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['vidi_frontend_cache']['options']['compression'] = 1;
//}