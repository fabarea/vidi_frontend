<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'Fab.vidi_frontend',
		'Pi1',
		'Vidi - List Component'
	);

	$TCA['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		'vidifrontend_pi1',
		sprintf('FILE:EXT:vidi_frontend/Configuration/FlexForm/VidiFrontend.xml')
	);

}

// Add static TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('vidi_frontend', 'Configuration/TypoScript', 'Vidi Frontend');
