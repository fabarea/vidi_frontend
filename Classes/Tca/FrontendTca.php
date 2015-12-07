<?php
namespace Fab\VidiFrontend\Tca;

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
use Fab\Vidi\Domain\Model\Content;
use Fab\Vidi\Exception\NotExistingClassException;
use Fab\Vidi\Tca\TcaServiceInterface;

/**
 * A class to handle TCA ctrl.
 */
class FrontendTca implements SingletonInterface, TcaServiceInterface
{

    /**
     * @var array
     */
    static protected $instances;

    /**
     * Returns a class instance of a corresponding TCA service.
     * If the class instance does not exist, create one.
     *
     * @throws NotExistingClassException
     * @param string $tableName
     * @param string $serviceType of the TCA. Typical values are: grid
     * @return TcaServiceInterface
     */
    static public function getService($tableName, $serviceType)
    {

        if (empty(self::$instances[$tableName][$serviceType])) {
            $className = sprintf('Fab\VidiFrontend\Tca\Frontend%sService', ucfirst($serviceType));

            if (!class_exists($className)) {
                throw new NotExistingClassException('Class does not exit: ' . $className, 1357060937);
            }

            $instance = GeneralUtility::makeInstance($className, $tableName, $serviceType);
            self::$instances[$tableName][$serviceType] = $instance;
        }

        return self::$instances[$tableName][$serviceType];
    }

    /**
     * Returns a "grid" service instance.
     *
     * @param string|Content $tableNameOrContentObject
     * @return \Fab\VidiFrontend\Tca\FrontendGridService
     */
    static public function grid($tableNameOrContentObject)
    {
        $tableName = $tableNameOrContentObject instanceof Content ? $tableNameOrContentObject->getDataType() : $tableNameOrContentObject;
        return self::getService($tableName, self::TYPE_GRID);
    }

}
