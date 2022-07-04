<?php
namespace Fab\VidiFrontend\Form\Elements;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

abstract class AbstractElement extends AbstractFormElement
{
    abstract public function render();

    protected function getPluginConfiguration(): array
    {
        $setup = $this->getConfigurationManager()->getTypoScriptSetup();

        $pluginConfiguration = [];
        if (is_array($setup['plugin.']['tx_vidifrontend.'])) {
            /** @var TypoScriptService $typoScriptService */
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_vidifrontend.']);
        }
        return $pluginConfiguration;
    }

    protected function getDataTypeFromFlexform(array $flexform = []): string
    {
        return $this->getSettings($flexform, 'dataType');
    }

    protected function getSettings(array $flexform, $key): string
    {
        $value = '';

        if (0 !== count($flexform)) {

            $normalizedFlexform = $this->normalizeFlexForm($flexform);
            if (!empty($normalizedFlexform['settings'][$key])) {
                $value = $normalizedFlexform['settings'][$key];
                if (is_array($value)) {
                    $value = $value[0];
                }
            }
        }
        return $value;
    }

    /**
     * Parses the flexForm content and converts it to an array
     * The resulting array will be multi-dimensional, as a value "bla.blubb"
     * results in two levels, and a value "bla.blubb.bla" results in three levels.
     *
     * Note: multi-language flexForms are not supported yet
     *
     * @param array $flexForm flexForm xml string
     * @param string $languagePointer language pointer used in the flexForm
     * @param string $valuePointer value pointer used in the flexForm
     * @return array the processed array
     */
    protected function normalizeFlexForm(array $flexForm, $languagePointer = 'lDEF', $valuePointer = 'vDEF'): array
    {
        $settings = [];
        $flexForm = isset($flexForm['data']) ? $flexForm['data'] : [];
        foreach (array_values($flexForm) as $languages) {
            if (!is_array($languages[$languagePointer])) {
                continue;
            }
            foreach ($languages[$languagePointer] as $valueKey => $valueDefinition) {
                if (strpos($valueKey, '.') === false) {
                    $settings[$valueKey] = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                } else {
                    $valueKeyParts = explode('.', $valueKey);
                    $currentNode = &$settings;
                    foreach ($valueKeyParts as $valueKeyPart) {
                        $currentNode = &$currentNode[$valueKeyPart];
                    }
                    if (is_array($valueDefinition)) {
                        if (array_key_exists($valuePointer, $valueDefinition)) {
                            $currentNode = $valueDefinition[$valuePointer];
                        } else {
                            $currentNode = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                        }
                    } else {
                        $currentNode = $valueDefinition;
                    }
                }
            }
        }
        return $settings;
    }


    /**
     * Parses a flexForm node recursively and takes care of sections etc
     *
     * @param array $nodeArray The flexForm node to parse
     * @param string $valuePointer The valuePointer to use for value retrieval
     * @return array
     */
    protected function walkFlexFormNode($nodeArray, $valuePointer = 'vDEF'): array
    {
        if (is_array($nodeArray)) {
            $return = [];
            foreach ($nodeArray as $nodeKey => $nodeValue) {
                if ($nodeKey === $valuePointer) {
                    return $nodeValue;
                }
                if (in_array($nodeKey, array('el', '_arrayContainer'))) {
                    return $this->walkFlexFormNode($nodeValue, $valuePointer);
                }
                if ($nodeKey[0] === '_') {
                    continue;
                }
                if (strpos($nodeKey, '.')) {
                    $nodeKeyParts = explode('.', $nodeKey);
                    $currentNode = &$return;
                    $nodeKeyPartsCount = count($nodeKeyParts);
                    for ($i = 0; $i < $nodeKeyPartsCount - 1; $i++) {
                        $currentNode = &$currentNode[$nodeKeyParts[$i]];
                    }
                    $newNode = array(next($nodeKeyParts) => $nodeValue);
                    $currentNode = $this->walkFlexFormNode($newNode, $valuePointer);
                } elseif (is_array($nodeValue)) {
                    if (array_key_exists($valuePointer, $nodeValue)) {
                        $return[$nodeKey] = $nodeValue[$valuePointer];
                    } else {
                        $return[$nodeKey] = $this->walkFlexFormNode($nodeValue, $valuePointer);
                    }
                } else {
                    $return[$nodeKey] = $nodeValue;
                }
            }
            return $return;
        }
        return $nodeArray;
    }

    protected function getConfigurationManager(): BackendConfigurationManager
    {
        return GeneralUtility::makeInstance(BackendConfigurationManager::class);
    }
}
