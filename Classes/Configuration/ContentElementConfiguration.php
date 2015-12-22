<?php
namespace Fab\VidiFrontend\Configuration;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Retrieve the configuration of a content element.
 */
class ContentElementConfiguration implements SingletonInterface
{
    /**
     * @var int
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    static protected $instances = [];

    /**
     * Returns a class instance.
     *
     * @param array $contentData
     * @return ContentElementConfiguration
     */
    static public function getInstance(array $contentData = [])
    {
        if (empty($contentData)) {
            $identifier = (int)GeneralUtility::_GP('identifier');
        } else {
            $identifier = $contentData['uid'];
        }

        if (empty(self::$instances[$identifier])) {
            if (empty($contentData)) {
                $contentData = self::getDatabaseConnection()->exec_SELECTgetSingleRow('pi_flexform', 'tt_content', 'uid = ' . $identifier);
            }
            $flexform = GeneralUtility::xml2array($contentData['pi_flexform']);
            self::$instances[$identifier] = GeneralUtility::makeInstance(ContentElementConfiguration::class, $identifier, $flexform);
        }
        return self::$instances[$identifier];
    }

    /**
     * Constructor
     *
     * @param int $identifier
     * @param array $flexform
     */
    public function __construct($identifier, array $flexform)
    {
        $this->identifier = $identifier;
        $normalizedFlexform = $this->normalizeFlexForm($flexform);
        $this->settings = $normalizedFlexform['settings'];
    }

    /**
     * Returns the columns configuration
     *
     * @return string
     */
    public function getColumnList()
    {
        return $this->settings['columns'];
    }

    /**
     * Returns the configured columns list.
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->settings['dataType'];
    }

    /**
     * Returns the columns configuration.
     *
     * @return array
     */
    public function getColumns()
    {
        $columnList = $this->getColumnList();
        $dataType = $this->getDataType();
        return ColumnsConfiguration::getInstance()->get($dataType, $columnList);
    }

    /**
     * Returns the columns names.
     *
     * @return array
     */
    public function getColumnsNames()
    {
        $columnList = $this->getColumnList();
        $columnNames = GeneralUtility::trimExplode(',', $columnList, TRUE);
        return $columnNames;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
    protected function normalizeFlexForm(array $flexForm, $languagePointer = 'lDEF', $valuePointer = 'vDEF')
    {
        $settings = array();
        $flexForm = isset($flexForm['data']) ? $flexForm['data'] : array();
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
    protected function walkFlexFormNode($nodeArray, $valuePointer = 'vDEF')
    {
        if (is_array($nodeArray)) {
            $return = array();
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

    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    static protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
