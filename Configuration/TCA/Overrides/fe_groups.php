<?php
use TYPO3\CMS\Core\Utility\ArrayUtility;
if (!defined('TYPO3')) die ('Access denied.');

$tca = array(
    'grid_frontend' => [
        'columns' => [

            # Overrides the "__buttons" column from the Vidi BE module.
            '__buttons' => [
                'renderer' => 'Fab\VidiFrontend\Grid\ShowButtonRenderer',
                'sortable' => false,
            ],
        ],
    ],
);

ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_groups'], $tca);
