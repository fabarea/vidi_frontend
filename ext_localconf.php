<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use Fab\VidiFrontend\Form\Elements\EnableFieldsElement;
use Fab\VidiFrontend\Form\Elements\TemplateMenuElement;
use Fab\VidiFrontend\Form\Elements\AdditionalSettingsListHelpElement;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use Fab\VidiFrontend\Controller\ContentController;
use Fab\VidiFrontend\Controller\TemplateBasedContentController;

defined('TYPO3') or die();

call_user_func(
    function () {

        $configuration = $configuration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class
        )->get('vidi_frontend');

        if (!isset($configuration['autoload_typoscript']) || true === (bool)$configuration['autoload_typoscript']) {

            ExtensionManagementUtility::addTypoScript(
                'vidi_frontend',
                'constants',
                '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:vidi_frontend/Configuration/TypoScript/constants.txt">'
            );

            ExtensionManagementUtility::addTypoScript(
                'vidi_frontend',
                'setup',
                '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:vidi_frontend/Configuration/TypoScript/setup.txt">'
            );
        }

        ExtensionUtility::configurePlugin(
            'VidiFrontend',
            'Pi1',
            [
                ContentController::class => 'index, list, show, execute',
            ],
            [
                ContentController::class => isset($configuration['non_cacheable_actions'])
                    ? $configuration['non_cacheable_actions']
                    : 'list, execute',
            ]
        );

        $nonCacheableControllerActions = !isset($configuration['is_index_view_cached']) || (bool)$configuration['is_index_view_cached']
            ? 'list'
            : 'index, list';

        ExtensionUtility::configurePlugin(
            'VidiFrontend',
            'TemplateBasedContent',
            [
                TemplateBasedContentController::class => 'index, list, show',
            ],
            [
                TemplateBasedContentController::class => $nonCacheableControllerActions,
            ]
        );

        // Register icons
        $icons = [
            'content-vidi-frontend' => 'EXT:vidi_frontend/Resources/Public/Images/VidiFrontend.png',
        ];
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier, BitmapIconProvider::class, ['source' => $path]
            );
        }

        ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    #header = LLL:EXT:sitepackage/Resources/Private/Language/newContentElements.xlf:extra
                    elements {
                        tx_vidifrontend_pi1 {
                            iconIdentifier = content-vidi-frontend
                            title = LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:wizard.title
                            description = LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:wizard.description
                            tt_content_defValues {
                                CType = list
                                list_type = vidifrontend_pi1
                            }
                        }
                        tx_vidifrontend_templatebasedcontent {
                            iconIdentifier = content-vidi-frontend
                            title = LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:wizard_template_based.title
                            description = LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:wizard_template_based.description
                            tt_content_defValues {
                                CType = list
                                list_type = vidifrontend_templatebasedcontent
                            }
                        }
                    }
                }
            }'
        );

        // Register new nodes
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1656697370] = [
            'nodeName' => 'vidiFrontendEnableFields',
            'priority' => 40,
            'class' => EnableFieldsElement::class,
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1656697371] = [
            'nodeName' => 'vidiFrontendTemplateMenu',
            'priority' => 40,
            'class' => TemplateMenuElement::class,
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1656697372] = [
            'nodeName' => 'vidiFrontendAdditionalSettingsListHelpListTemplates',
            'priority' => 40,
            'class' => AdditionalSettingsListHelpElement::class,
            'parameters' => [
                'typoscriptConfigurationKey' => 'listTemplates',
            ],
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1656697373] = [
            'nodeName' => 'vidiFrontendAdditionalSettingsListHelpTemplates',
            'priority' => 40,
            'class' => AdditionalSettingsListHelpElement::class,
            'parameters' => [
                'typoscriptConfigurationKey' => 'template',
            ],
        ];

        // Connect "preProcessTca" signal slot with the metadata service.
        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $signalSlotDispatcher->connect(
            'Fab\Vidi\Tca\Tca',
            'preProcessTca',
            'Fab\VidiFrontend\Configuration\TcaTweak',
            'tweakFileReferences',
            true
        );
    }
);
