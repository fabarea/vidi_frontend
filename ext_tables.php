<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
defined('TYPO3') or die();

call_user_func(
    function () {

        $configuration = $configuration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class
        )->get('vidi_frontend');

        // Possible Static TS loading
        if (true === isset($configuration['autoload_typoscript']) && true === (bool)$configuration['autoload_typoscript']) {
            ExtensionManagementUtility::addStaticFile('vidi_frontend', 'Configuration/TypoScript', 'Vidi Frontend: generic List Component');
        }

        if (TYPO3_MODE === 'BE') {

            // Register plugin "pi1"
            ExtensionUtility::registerPlugin(
                'VidiFrontend',
                'Pi1',
                'LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:plugin.pi1'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';
            ExtensionManagementUtility::addPiFlexFormValue(
                'vidifrontend_pi1',
                'FILE:EXT:vidi_frontend/Configuration/FlexForm/VidiFrontend.xml'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['vidifrontend_pi1'] = 'layout, select_key, pages, recursive';
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';

            // Register plugin "TemplateBasedContent"
            ExtensionUtility::registerPlugin(
                'VidiFrontend',
                'TemplateBasedContent',
                'LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:plugin.templateBasedContent'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_templatebasedcontent'] = 'pi_flexform';
            ExtensionManagementUtility::addPiFlexFormValue(
                'vidifrontend_templatebasedcontent',
                'FILE:EXT:vidi_frontend/Configuration/FlexForm/TemplateBasedContent.xml'
            );

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['vidifrontend_templatebasedcontent'] = 'layout, select_key, pages, recursive';
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_templatebasedcontent'] = 'pi_flexform';

        }
    }
);
