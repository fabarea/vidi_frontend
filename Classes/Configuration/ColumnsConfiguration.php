<?php
namespace Fab\VidiFrontend\Configuration;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Retrieve columns configuration given CSV list of columns.
 */
class ColumnsConfiguration
{

    /**
     * Returns the configuration of a content element.
     *
     * @return ColumnsConfiguration
     * @throws \InvalidArgumentException
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(ColumnsConfiguration::class);
    }

    /**
     * Returns the columns configuration given CSV list of columns.
     *
     * @param string $dataType
     * @param string $columnList
     * @return array
     */
    public function get($dataType, $columnList = '')
    {

        $configuration = [];
        $columns = GeneralUtility::trimExplode(',', $columnList, TRUE);
        foreach ($columns as $fieldNameAndPath) {
            $configuration[$fieldNameAndPath] = FrontendTca::grid($dataType)->getField($fieldNameAndPath);
        }
        return $configuration;
    }

}
