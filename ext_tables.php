<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(
    function () {
        $configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['vidi_frontend']);

        // Possible Static TS loading
        if (true === isset($configuration['autoload_typoscript']) && true === (bool)$configuration['autoload_typoscript']) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('vidi_frontend', 'Configuration/TypoScript', 'Vidi Frontend: generic List Component');
        }

        if (TYPO3_MODE === 'BE') {

            // Register plugin "pi1"
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
                'Fab.vidi_frontend',
                'Pi1',
                'LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:plugin.pi1'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                'vidifrontend_pi1',
                'FILE:EXT:vidi_frontend/Configuration/FlexForm/VidiFrontend.xml'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['vidifrontend_pi1'] = 'layout, select_key, pages, recursive';
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';

            // Register plugin "TemplateBasedContent"
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
                'Fab.vidi_frontend',
                'TemplateBasedContent',
                'LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:plugin.templateBasedContent'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_templatebasedcontent'] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                'vidifrontend_templatebasedcontent',
                'FILE:EXT:vidi_frontend/Configuration/FlexForm/TemplateBasedContent.xml'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['vidifrontend_templatebasedcontent'] = 'layout, select_key, pages, recursive';
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_templatebasedcontent'] = 'pi_flexform';

        }
    }
);