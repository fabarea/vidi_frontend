<?php

if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
    'grid_frontend' => [
        'columns' => [

            # Just an example how to override the "usergroup" column from the Vidi BE module.
            #'usergroup' => [
            #    'renderers' => [
            #        'Vendor\MyExt\Grid\MyRenderer',
            #    ],
            #    'label' => 'LLL:EXT:vidi/Resources/Private/Language/fe_users.xlf:usergroup',
            #],

            # Overrides the "__buttons" column from the Vidi BE module.
            '__buttons' => [
                'renderer' => 'Fab\VidiFrontend\Grid\ShowButtonRenderer',
                'sortable' => false
            ],
        ],
        'actions' => [
            #new \Vendor\MyExt\MassAction\MyAction(),
        ]
    ],
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);