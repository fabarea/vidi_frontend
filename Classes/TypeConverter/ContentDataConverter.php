<?php
namespace Fab\VidiFrontend\TypeConverter;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Fab\Vidi\Service\DataService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

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
     * @throws \RuntimeException
     * @return array
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $record = $this->getDataService()->getRecord(
            'tt_content',
            [
                'uid' => $source,
                'pid' => self::getFrontendObject()->id,
            ]
        );
        if (empty($record)) {
            throw new \RuntimeException('Vidi Frontend: I could not access this resource', 1445352723);
        }

        return $record;
    }

    /**
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return PageRepository
     */
    static protected function getPageRepository()
    {
        return self::getFrontendObject()->sys_page;
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return TypoScriptFrontendController
     */
    static protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }
}
