<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'grid_frontend' => array(
		'facets' => array(
			'uid',
			'username',
			'first_name',
			'last_name',
			'usergroup',
			new \TYPO3\CMS\Vidi\Facet\StandardFacet(
				'disable',
				'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active',
				array(
					'0' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.0',
					'1' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.1'
				)
			),
		),
		'columns' => array(
			'uid' => array(
				'label' => 'Id',
				'width' => '5px',
			),
			'username' => array(
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_users.xlf:username',
			),
			'name' => array(
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_users.xlf:name',
			),
			'email' => array(
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_users.xlf:email',
			),
			'usergroup' => array(
				'renderers' => array(
					'TYPO3\CMS\Vidi\Grid\RelationEditRenderer',
					'TYPO3\CMS\Vidi\Grid\RelationRenderer',
				),
				'editable' => TRUE,
				'sortable' => FALSE,
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_users.xlf:usergroup',
			),
			'disable' => array(
				'renderer' => 'TYPO3\CMS\Vidi\Grid\VisibilityRenderer',
				'label' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active',
				'width' => '3%',
			),
		),
	),
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);
