<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Fab.vidi_frontend',
    'Pi1',
    array(
        'Content' => 'index, list, show, execute, warn',
    ),
    array(
        'Content' => 'list, execute',
    )
);

// Connect "preProcessTca" signal slot with the metadata service.
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
$signalSlotDispatcher->connect(
    'Fab\Vidi\Tca\Tca',
    'preProcessTca',
    'Fab\VidiFrontend\Configuration\TcaTweak',
    'tweakFileReferences',
    TRUE
);
