<?php
namespace Fab\VidiFrontend\TypeConverter;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Service\ContentType;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;

/**
 * Convert a content identifier into a Content object.
 */
class ContentConverter extends AbstractTypeConverter
{

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('int');

    /**
     * @var string
     */
    protected $targetType = 'Fab\Vidi\Domain\Model\Content';

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
     * @return \Fab\Vidi\Domain\Model\Content|mixed|null|\TYPO3\CMS\Extbase\Error\Error
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {

        $dataType = $this->getContentType()->getCurrentType();

        $contentRepository = ContentRepositoryFactory::getInstance($dataType);
        $content = $contentRepository->findByIdentifier((int)$source);
        return $content;
    }

    /**
     * @return ContentType|object
     */
    protected function getContentType()
    {
        return GeneralUtility::makeInstance(ContentType::class);
    }

}
