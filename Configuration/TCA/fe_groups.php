<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'grid_frontend' => array(
		'facets' => array(
			'uid',
			'title',
			'description',
		),
		'columns' => array(
			'uid' => array(
				'label' => 'Id',
				'width' => '5px',
			),
			'title' => array(
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_groups.xlf:title',
			),
		),
	),
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_groups'], $tca);