<?php
namespace Fab\VidiFrontend\TypeConverter;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;

/**
 * Convert a weird array given as input by DataTables into a key value array.
 */
class ArrayConverter extends AbstractTypeConverter {

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('array');

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
	 * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
	 * @return File
	 * @api
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL) {
		$listOfValues = '';
		if (isset($source[0])) {
			$listOfValues = $source[0];
		}
		return GeneralUtility::trimExplode(',', $listOfValues, TRUE);
	}

}