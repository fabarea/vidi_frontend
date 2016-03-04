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
