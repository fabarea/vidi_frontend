<?php
namespace Fab\VidiFrontend\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Grid\ColumnRendererAbstract;
use Fab\VidiFrontend\Configuration\ContentElementConfiguration;
use Fab\VidiFrontend\Plugin\PluginParameter;
use Fab\Vidi\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Class for editing mm relation between objects.
 */
class ShowButtonRenderer extends ColumnRendererAbstract
{

    /**
     * Render a representation of the relation on the GUI.
     *
     * @return string
     */
    public function render()
    {

        /** @var TagBuilder $tagBuilder */
        $tagBuilder = $this->getObjectManager()->get(TagBuilder::class);
        $tagBuilder->reset();
        $tagBuilder->setTagName('a');
        #$tagBuilder->setContent('<span class="glyphicon glyphicon-eye-open"></span>'); // Only if Font Awesome is installed.
        $icon = sprintf(
            '<img src="/%sResources/Public/Build/Images/show_property.png" alt="" width="16">',
            ExtensionManagementUtility::siteRelPath('vidi_frontend')
        );
        $tagBuilder->setContent($icon);

        $arguments = $this->getArguments();
        $this->getUriBuilder()->setArguments($arguments);

        $settings =  ContentElementConfiguration::getInstance()->getSettings();
        $targetPageUid = (int)$settings['targetPageDetail'];
        if (!empty($targetPageUid)) {
            $this->getUriBuilder()->setTargetPageUid($targetPageUid);
        }

        $uri = $this->getUriBuilder()->build();

        $tagBuilder->addAttribute('href', $uri);
        $tagBuilder->addAttribute('class', 'link-show');
        $tagBuilder->addAttribute('title', LocalizationUtility::translate('link.showDetail', 'vidi_frontend'));
        return $tagBuilder->render();
    }

    /**
     * @return array
     */
    protected function getArguments()
    {

        $contentElementIdentifier =  ContentElementConfiguration::getInstance()->getIdentifier();
        $settings =  ContentElementConfiguration::getInstance()->getSettings();

        if (empty(trim($settings['parameterPrefix'])) || $settings['parameterPrefix'] === PluginParameter::PREFIX) {
            $arguments = array(
                PluginParameter::PREFIX => array(
                    'contentElement' => $contentElementIdentifier,
                    'action' => 'show',
                    'content' => $this->object->getUid(),
                ),
            );
        } else {
            $parts = GeneralUtility::trimExplode('|', $settings['parameterPrefix']);
            if (count($parts) === 1) {
                $parameterName = $parts[0];
                $arguments = [
                    $parameterName => $this->object->getUid(),
                ];
            } else {
                $parameterPrefix = $parts[0];
                $parameterName = $parts[1];
                $arguments = [
                    $parameterPrefix => [
                        $parameterName => $this->object->getUid(),
                    ],
                ];
            }
        }

        return $arguments;
    }

    /**
     * @return ObjectManager
     * @throws \InvalidArgumentException
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
     */
    protected function getUriBuilder()
    {
        $configuration = $this->getGridRendererConfiguration();
        return $configuration['uriBuilder'];
    }

}
