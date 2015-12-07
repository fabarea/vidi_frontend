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
     * @var array
     */
    protected $configuration = array();

    /**
     * @var array
     */
    protected $flexform = array();

    /**
     * @var array
     */
    static protected $instances = array();

    /**
     * Returns a class instance.
     *
     * @param int $identifier
     * @return \Fab\VidiFrontend\Configuration\ContentElementConfiguration
     */
    static public function getInstance($identifier = 0)
    {
        if (empty(self::$instances[$identifier])) {
            if ((int)$identifier === 0) {
                $identifier = GeneralUtility::_GP('identifier');
            }
            $records = self::getDatabaseConnection()->exec_SELECTgetSingleRow('pi_flexform', 'tt_content', 'uid = ' . (int)$identifier);
            $flexform = GeneralUtility::xml2array($records['pi_flexform']);
            self::$instances[$identifier] = GeneralUtility::makeInstance('Fab\VidiFrontend\Configuration\ContentElementConfiguration', $flexform);
        }
        return self::$instances[$identifier];
    }

    /**
     * Constructor
     */
    public function __construct($flexform)
    {
        $this->flexform = $flexform;
    }

    /**
     * Returns the columns configuration
     *
     * @return string
     */
    public function getColumnList()
    {

        if (empty($this->configuration['columns'])) {
            $columns = '';
            if (!empty($this->flexform['data']['general']['lDEF']['settings.columns']['vDEF'])) {
                $columns = $this->flexform['data']['general']['lDEF']['settings.columns']['vDEF'];
            }
            $this->configuration['columns'] = $columns;
        }
        return $this->configuration['columns'];
    }

    /**
     * Returns the configured columns list.
     *
     * @return string
     */
    public function getDataType()
    {

        if (empty($this->configuration['dataType'])) {
            $columns = '';
            if (!empty($this->flexform['data']['general']['lDEF']['settings.dataType']['vDEF'])) {
                $columns = $this->flexform['data']['general']['lDEF']['settings.dataType']['vDEF'];
            }
            $this->configuration['dataType'] = $columns;
        }
        return $this->configuration['dataType'];
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
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    static protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
