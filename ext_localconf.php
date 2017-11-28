<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vidi_frontend']);

if (!isset($configuration['autoload_typoscript']) || true === (bool)$configuration['autoload_typoscript']) {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'vidi_frontend',
        'constants',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:vidi_frontend/Configuration/TypoScript/constants.txt">'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'vidi_frontend',
        'setup',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:vidi_frontend/Configuration/TypoScript/setup.txt">'
    );
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Fab.vidi_frontend',
    'Pi1',
    [
        'Content' => 'index, list, show, execute',
    ],
    [
        'Content' => 'list, execute',
    ]
);

$nonCacheableControllerActions = !isset($configuration['is_index_view_cached']) || (bool)$configuration['is_index_view_cached']
    ? 'list'
    : 'index, list';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Fab.vidi_frontend',
    'TemplateBasedContent',
    [
        'TemplateBasedContent' => 'index, list, show',
    ],
    [
        'TemplateBasedContent' => $nonCacheableControllerActions,
    ]
);

// Connect "preProcessTca" signal slot with the metadata service.
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$signalSlotDispatcher->connect(
    'Fab\Vidi\Tca\Tca',
    'preProcessTca',
    'Fab\VidiFrontend\Configuration\TcaTweak',
    'tweakFileReferences',
    true
);
