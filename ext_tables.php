<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Fab.vidi_frontend',
        'Pi1',
        'Generic List Component'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'vidifrontend_pi1',
        sprintf('FILE:EXT:vidi_frontend/Configuration/FlexForm/VidiFrontend.xml')
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['vidifrontend_pi1'] = 'layout, select_key, pages, recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';

    $GLOBALS['TBE_MODULES_EXT']["xMOD_db_new_content_el"]['addElClasses']['Fab\VidiFrontend\Backend\Wizard'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('vidi_frontend') . 'Classes/Backend/Wizard.php';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Vidi Frontend');
}
