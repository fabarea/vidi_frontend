<?php
namespace Fab\VidiFrontend\TypeConverter;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;

/**
 * Convert a content identifier to an data array.
 */
class ContentDataConverter extends AbstractTypeConverter
{

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('int');

    /**
     * @var string
     */
    protected $targetType = 'array';

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * Actually convert from $source to $targetType
     *
     * @param string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @throws \Exception
     * @return array
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {

        $tableName = 'tt_content';
        $clause = 'uid = ' . $source;
        $clause .= ' AND pid = ' . self::getFrontendObject()->id; // Make sure we are on the same pid for security reason
        $clause .= self::getPageRepository()->enableFields($tableName);
        $clause .= self::getPageRepository()->deleteClause($tableName);
        $record = self::getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);

        if (empty($record)) {
            throw new \RuntimeException('Vidi Frontend: I could not access this resource', 1445352723);
        }

        return $record;
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

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    static protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    static protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }
}
