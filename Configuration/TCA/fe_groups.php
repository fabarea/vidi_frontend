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
			'title' => array(
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_groups.xlf:title',
			),
			'__buttons' => array(
				'renderer' => 'Fab\VidiFrontend\Grid\ShowButtonRenderer',
				'sortable' => FALSE,
			),
		),
	),
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_groups'], $tca);