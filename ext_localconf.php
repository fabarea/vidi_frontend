<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'vidi_frontend',
	'Pi1',
	array(
		'Request' => 'index, list',
	),
	array(
		'Request' => 'list',
	)
);
