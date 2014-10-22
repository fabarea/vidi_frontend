<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Fab.vidi_frontend',
	'Pi1',
	array(
		'Content' => 'index, list, show',
	),
	array(
		'Content' => 'list, show',
	)
);
