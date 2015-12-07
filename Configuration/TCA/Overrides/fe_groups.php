<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
    'grid_frontend' => [
        'columns' => [

            # Overrides the "__buttons" column from the Vidi BE module.
            '__buttons' => [
                'renderer' => 'Fab\VidiFrontend\Grid\ShowButtonRenderer',
                'sortable' => FALSE,
            ],
        ],
    ],
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_groups'], $tca);